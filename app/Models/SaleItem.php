<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'item_id',
        'quantity',
        'price',
        'code',
        'refunded_quantity',
        'as_gift',
        'special_discount',
        'subtotal',
        'is_exchanged'  // Added the is_exchanged field
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function exchange($newItemId, $newQuantity, $newPrice)
    {
        // Update inventory for the old item
        $this->item->increment('quantity', $this->quantity);

        // Calculate subtotal
        $subtotal = $newPrice * $newQuantity;

        // Update the sale item with new details
        $this->update([
            'item_id' => $newItemId,
            'quantity' => $newQuantity,
            'price' => $newPrice,
            'special_discount' => 0,  // Reset special discount on exchange
            'subtotal' => $subtotal   // Set the subtotal
        ]);

        // Update inventory for the new item
        $newItem = Item::find($newItemId);
        $newItem->decrement('quantity', $newQuantity);
    }
}
