<?php $__env->startSection('content'); ?>
<div class="container">
    <h1>Create New Sale</h1>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('sales.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>

        <div class="mb-3">
            <label for="barcode" class="form-label">Barcode</label>
            <input type="text" class="form-control" id="barcode" name="barcode">
            <button type="button" id="searchByBarcode" class="btn btn-info mt-2">Search Item</button>
        </div>

        <div class="mb-3">
            <label for="brand" class="form-label">Brand</label>
            <select class="form-select" id="brand" name="brand_id" required>
                <option value="">Select Brand</option>
                <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($brand->id); ?>"><?php echo e($brand->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select class="form-select" id="category" name="category_id" required>
                <option value="">Select Category</option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($category->id); ?>"><?php echo e($category->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="item" class="form-label">Item</label>
            <select class="form-select" id="item" name="item_id" required>
                <option value="">Select Item</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" class="form-control" id="quantity" name="quantity" required>
        </div>

        <div id="itemList" class="mb-3"></div>

        <button type="button" id="addItemButton" class="btn btn-secondary mb-3">Add Item</button>
        <button type="submit" class="btn btn-primary">Create Sale</button>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Fetch items based on selected brand and category
    document.getElementById('brand').addEventListener('change', updateItems);
    document.getElementById('category').addEventListener('change', updateItems);

    function updateItems(callback = null) {
        const brandId = document.getElementById('brand').value;
        const categoryId = document.getElementById('category').value;

        if (brandId && categoryId) {
            fetch(`/api/items/filter?brand_id=${brandId}&category_id=${categoryId}`)
                .then(response => response.json())
                .then(data => {
                    const itemSelect = document.getElementById('item');
                    itemSelect.innerHTML = '<option value="">Select Item</option>';

                    data.forEach(item => {
                        itemSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                    });

                    if (callback) callback(); // Execute the callback to select the item
                })
                .catch(error => console.error('Error fetching items:', error));
        }
    }

    // Search by Barcode
document.getElementById('searchByBarcode').addEventListener('click', function() {
    const barcode = document.getElementById('barcode').value;

    if (barcode) {
        fetch(`/api/items/search?barcode=${barcode}`)
            .then(response => response.json())
            .then(data => {
                if (data) {
                    document.getElementById('brand').value = data.brand_id;
                    document.getElementById('category').value = data.category_id;

                    // Automatically add the item to the sale with quantity set to 1
                    addItemToSale(data.id, data.name); // Call function to add item
                    document.getElementById('quantity').value = '1'; // Set quantity to 1

                    // Optionally update items dropdown if you need
                    updateItems(() => {
                        document.getElementById('item').value = data.id; // Select the correct item after dropdown is populated
                    });
                } else {
                    alert('No item found with the given barcode.');
                }
            })
            .catch(error => console.error('Error searching item by barcode:', error));
    }
});


    // Add item button listener
    document.getElementById('addItemButton').addEventListener('click', function() {
        const itemId = document.getElementById('item').value; // Get selected item ID
        const itemName = document.getElementById('item').options[document.getElementById('item').selectedIndex].text; // Get selected item name
        addItemToSale(itemId, itemName);
    });

    // Function to add item to sale
    function addItemToSale(itemId, itemName) {
    const quantity = 1; // Set quantity to 1 for barcode scans

    if (itemId) {
        const itemList = document.getElementById('itemList');
        
        // Create a new list item
        const listItem = document.createElement('div');
        listItem.className = 'item'; // Optional: add a class for styling
        listItem.innerHTML = `${itemName} (Qty: ${quantity}) 
            <input type="hidden" name="items[][item_id]" value="${itemId}">
            <input type="hidden" name="items[][quantity]" value="${quantity}">`;
        
        // Append the new list item to the item list
        itemList.appendChild(listItem);
        
        // Clear the barcode field for the next scan
        document.getElementById('barcode').value = '';
    } else {
        alert('Invalid item.');
    }
}

});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/sales/create.blade.php ENDPATH**/ ?>