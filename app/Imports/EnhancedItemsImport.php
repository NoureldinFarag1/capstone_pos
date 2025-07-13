<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Size;
use App\Models\Color;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Picqer\Barcode\BarcodeGeneratorPNG;

class EnhancedItemsImport implements
    ToCollection,
    WithHeadingRow,
    WithValidation,
    SkipsOnError,
    SkipsOnFailure,
    WithBatchInserts,
    WithChunkReading
{
    protected $results = [
        'success' => 0,
        'errors' => [],
        'warnings' => [],
        'created_items' => [],
        'updated_items' => []
    ];

    protected $brandCache = [];
    protected $categoryCache = [];
    protected $sizeCache = [];
    protected $colorCache = [];

    public function __construct()
    {
        // Pre-load all lookup data for performance
        $this->brandCache = Brand::pluck('id', 'name')->toArray();
        $this->categoryCache = Category::pluck('id', 'name')->toArray();
        $this->sizeCache = Size::pluck('id', 'name')->toArray();
        $this->colorCache = Color::pluck('id', 'name')->toArray();
    }

    public function collection(Collection $rows)
    {
        DB::beginTransaction();

        try {
            // Log first row for debugging column headers
            if ($rows->count() > 0) {
                $firstRow = $rows->first();
                Log::info('Import column headers: ' . implode(', ', array_keys($firstRow->toArray())));
            }

            $groupedItems = $this->groupItemsByParent($rows);

            foreach ($groupedItems as $parentItemData) {
                $this->processItemGroup($parentItemData);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Bulk import failed: ' . $e->getMessage());
            $this->results['errors'][] = 'Import failed: ' . $e->getMessage();
        }
    }

    protected function groupItemsByParent(Collection $rows)
    {
        $grouped = [];

        foreach ($rows as $index => $row) {
            // Clean and normalize row data
            $row = $row->map(function ($value) {
                return is_string($value) ? trim($value) : $value;
            });

            // Skip completely empty rows
            if ($this->isRowEmpty($row)) {
                continue;
            }

            // Validate required fields
            $requiredFields = ['item_name', 'brand', 'selling_price'];
            $missingFields = [];

            foreach ($requiredFields as $field) {
                if (empty($row[$field])) {
                    $missingFields[] = $field;
                }
            }

            if (!empty($missingFields)) {
                $this->results['errors'][] = "Row " . ($index + 2) . ": Missing required fields: " . implode(', ', $missingFields);
                continue;
            }

            $parentKey = $this->generateParentKey($row);

            if (!isset($grouped[$parentKey])) {
                $grouped[$parentKey] = [
                    'parent_data' => $this->extractParentData($row),
                    'variants' => []
                ];
            }

            $grouped[$parentKey]['variants'][] = [
                'row_number' => $index + 2,
                'data' => $row
            ];
        }

        return $grouped;
    }

    protected function isRowEmpty($row)
    {
        foreach ($row as $value) {
            if (!empty($value) && $value !== null && $value !== '') {
                return false;
            }
        }
        return true;
    }

    protected function generateParentKey($row)
    {
        return strtolower(trim($row['item_name'] ?? '')) .
               '_' . strtolower(trim($row['brand'] ?? '')) .
               '_' . strtolower(trim($row['category'] ?? ''));
    }

    protected function extractParentData($row)
    {
        return [
            'name' => $row['item_name'],
            'brand' => $row['brand'],
            'category' => $row['category'] ?? 'General',
            'description' => $row['description'] ?? '',
            'buying_price' => floatval($row['buying_price'] ?? $row['selling_price'] ?? 0),
            'selling_price' => floatval($row['selling_price'] ?? 0),
            'tax' => floatval($row['tax'] ?? 0),
            'discount_type' => $row['discount_type'] ?? 'percentage',
            'discount_value' => floatval($row['discount_value'] ?? 0),
        ];
    }

    protected function processItemGroup($itemGroup)
    {
        $parentData = $itemGroup['parent_data'];
        $variants = $itemGroup['variants'];

        // Validate parent data
        $brandId = $this->getOrCreateBrand($parentData['brand']);
        $categoryId = $this->getOrCreateCategory($parentData['category']);

        if (!$brandId || !$categoryId) {
            $this->results['errors'][] = "Invalid brand or category for item: " . $parentData['name'];
            return;
        }

        // Check if parent item already exists
        $existingParent = Item::where('name', $parentData['name'])
            ->where('brand_id', $brandId)
            ->where('category_id', $categoryId)
            ->where('is_parent', true)
            ->first();

        if (!$existingParent) {
            // Create new parent item
            $parentItem = $this->createParentItem($parentData, $brandId, $categoryId);
            $this->results['created_items'][] = $parentItem->name . ' (Parent)';
        } else {
            // Update existing parent item
            $parentItem = $this->updateParentItem($existingParent, $parentData);
            $this->results['updated_items'][] = $parentItem->name . ' (Parent)';
        }

        // Process variants
        $totalQuantity = 0;
        foreach ($variants as $variantInfo) {
            $quantity = $this->processVariant($parentItem, $variantInfo);
            $totalQuantity += $quantity;
        }

        // Update parent total quantity
        $parentItem->quantity = $totalQuantity;
        $parentItem->save();
    }

    protected function createParentItem($parentData, $brandId, $categoryId)
    {
        $parentItem = Item::create([
            'name' => $parentData['name'],
            'brand_id' => $brandId,
            'category_id' => $categoryId,
            'buying_price' => $parentData['buying_price'],
            'selling_price' => $parentData['selling_price'],
            'tax' => $parentData['tax'],
            'discount_type' => $parentData['discount_type'],
            'discount_value' => $parentData['discount_value'],
            'quantity' => 0, // Will be updated after variants
            'is_parent' => true,
        ]);

        // Generate parent barcode
        $parentBarcode = Str::padLeft($brandId, 3, '0') .
            Str::padLeft($categoryId, 3, '0') .
            Str::padLeft($parentItem->id, 4, '0');

        $parentItem->code = $parentBarcode;
        $parentItem->save();

        return $parentItem;
    }

    protected function updateParentItem($parentItem, $parentData)
    {
        $parentItem->update([
            'buying_price' => $parentData['buying_price'],
            'selling_price' => $parentData['selling_price'],
            'tax' => $parentData['tax'],
            'discount_type' => $parentData['discount_type'],
            'discount_value' => $parentData['discount_value'],
        ]);

        return $parentItem;
    }

    protected function processVariant($parentItem, $variantInfo)
    {
        $row = $variantInfo['data'];
        $rowNumber = $variantInfo['row_number'];

        // Get size and color with proper validation
        $sizeName = !empty($row['size']) ? (string)$row['size'] : 'N/A';
        $colorName = !empty($row['color']) ? (string)$row['color'] : 'N/A';
        $quantity = intval($row['quantity'] ?? 0);

        if ($quantity < 0) {
            $this->results['errors'][] = "Row $rowNumber: Quantity cannot be negative";
            return 0;
        }

        // Get or create size and color
        $sizeId = $this->getOrCreateSize($sizeName);
        $colorId = $this->getOrCreateColor($colorName, $row['color_code'] ?? '#000000');

        // Create variant name
        $variantName = $this->createVariantName($parentItem->name, $sizeName, $colorName);

        // Check if variant already exists
        $existingVariant = Item::where('parent_id', $parentItem->id)
            ->whereHas('sizes', function ($query) use ($sizeId) {
                $query->where('sizes.id', $sizeId);
            })
            ->whereHas('colors', function ($query) use ($colorId) {
                $query->where('colors.id', $colorId);
            })
            ->first();

        if ($existingVariant) {
            // Update existing variant
            $existingVariant->quantity = $quantity;
            $existingVariant->save();
            $this->results['updated_items'][] = $variantName;
            return $quantity;
        }

        // Create new variant
        $variant = Item::create([
            'name' => $variantName,
            'category_id' => $parentItem->category_id,
            'brand_id' => $parentItem->brand_id,
            'quantity' => $quantity,
            'buying_price' => $parentItem->buying_price,
            'selling_price' => $parentItem->selling_price,
            'tax' => $parentItem->tax,
            'discount_type' => $parentItem->discount_type,
            'discount_value' => $parentItem->discount_value,
            'parent_id' => $parentItem->id,
            'is_parent' => false,
        ]);

        // Generate variant barcode
        $this->generateVariantBarcode($variant, $parentItem, $sizeId, $colorId);

        // Attach size and color relationships
        $variant->sizes()->attach([$sizeId]);
        $variant->colors()->attach([$colorId]);

        $this->results['created_items'][] = $variantName;
        $this->results['success']++;

        return $quantity;
    }

    protected function createVariantName($parentName, $sizeName, $colorName)
    {
        $parts = [$parentName];

        if ($sizeName !== 'N/A') {
            $parts[] = $sizeName;
        }

        if ($colorName !== 'N/A') {
            $parts[] = $colorName;
        }

        return implode(' - ', $parts);
    }

    protected function generateVariantBarcode($variant, $parentItem, $sizeId, $colorId)
    {
        try {
            $variantBarcode = $parentItem->code .
                Str::padLeft($colorId, 2, '0') .
                Str::padLeft($sizeId, 2, '0');

            $barcodeGenerator = new BarcodeGeneratorPNG();
            $barcodePath = 'barcodes/' . $variantBarcode . '.png';
            $storagePath = storage_path('app/public/' . $barcodePath);

            // Ensure directory exists
            if (!file_exists(dirname($storagePath))) {
                mkdir(dirname($storagePath), 0755, true);
            }

            $barcodeImage = $barcodeGenerator->getBarcode(
                $variantBarcode,
                $barcodeGenerator::TYPE_CODE_128,
                3,
                50
            );

            if (file_put_contents($storagePath, $barcodeImage)) {
                $variant->barcode = $barcodePath;
                $variant->code = $variantBarcode;
                $variant->save();
            }
        } catch (\Exception $e) {
            Log::error('Barcode generation failed for variant: ' . $variant->id . ' - ' . $e->getMessage());
        }
    }

    protected function getOrCreateBrand($brandName)
    {
        if (empty($brandName)) {
            $brandName = 'General';
        }

        $normalizedName = trim($brandName);

        if (isset($this->brandCache[$normalizedName])) {
            return $this->brandCache[$normalizedName];
        }

        $brand = Brand::firstOrCreate(['name' => $normalizedName]);
        $this->brandCache[$normalizedName] = $brand->id;

        return $brand->id;
    }

    protected function getOrCreateCategory($categoryName)
    {
        if (empty($categoryName)) {
            $categoryName = 'General';
        }

        $normalizedName = trim($categoryName);

        if (isset($this->categoryCache[$normalizedName])) {
            return $this->categoryCache[$normalizedName];
        }

        $category = Category::firstOrCreate(['name' => $normalizedName]);
        $this->categoryCache[$normalizedName] = $category->id;

        return $category->id;
    }

    protected function getOrCreateSize($sizeName)
    {
        $normalizedName = trim($sizeName);

        if (isset($this->sizeCache[$normalizedName])) {
            return $this->sizeCache[$normalizedName];
        }

        // Create size with a default type value
        $size = Size::firstOrCreate(
            ['name' => $normalizedName],
            ['type' => 'general'] // Provide default type value
        );
        $this->sizeCache[$normalizedName] = $size->id;

        return $size->id;
    }

    protected function getOrCreateColor($colorName, $colorCode = '#000000')
    {
        $normalizedName = trim($colorName);

        if (isset($this->colorCache[$normalizedName])) {
            return $this->colorCache[$normalizedName];
        }

        $color = Color::firstOrCreate(
            ['name' => $normalizedName],
            ['hex_code' => $colorCode]
        );
        $this->colorCache[$normalizedName] = $color->id;

        return $color->id;
    }

    public function rules(): array
    {
        return [
            'item_name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer|min:0',
            'buying_price' => 'nullable|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'size' => 'nullable|max:50',
            'color' => 'nullable|string|max:50',
            'tax' => 'nullable|numeric|min:0|max:100',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:percentage,fixed',
        ];
    }

    public function onError(\Throwable $error)
    {
        $this->results['errors'][] = $error->getMessage();
    }

    public function onFailure(\Maatwebsite\Excel\Validators\Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->results['errors'][] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
        }
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function getResults()
    {
        return $this->results;
    }
}
