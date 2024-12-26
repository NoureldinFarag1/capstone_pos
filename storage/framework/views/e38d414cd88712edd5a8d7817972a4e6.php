<?php $__env->startSection('content'); ?>
<br>
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-3">
                <img src="<?php echo e(asset('storage/' . $item->picture)); ?>" alt="<?php echo e($item->name); ?>" class="card-img-top">
                <div class="card-body">
                    <h5 class="card-title"><?php echo e($item->name); ?></h5>
                    <div class="row">
                        <div class="col-6">
                            <?php if($item->barcode): ?>
                                <img src="<?php echo e(asset('storage/' .$item->barcode)); ?>" alt="Barcode" class="img-fluid">
                                <p class="card-text">Barcode: <?php echo e($item->code); ?></p>
                            <?php else: ?>
                                <p class="card-text">No barcode available.</p>
                            <?php endif; ?>
                        </div>
                        <div class="col-6">
                            <p class="card-text"><strong>Brand:</strong> <?php echo e($item->brand->name); ?></p>
                            <p class="card-text"><strong>Category:</strong> <?php echo e($item->category->name); ?></p>
                            <p class="card-text"><strong>Price:</strong> $<?php echo e($item->priceAfterSale()); ?></p>
                            <?php if($item->discount_type === 'percentage'): ?>
                                <p class="mb-1 text-muted">Sale: <span class="fw-bold"><?php echo e($item->discount_value); ?>%</span></p>
                            <?php else: ?>
                                <p class="mb-1 text-muted">Sale: <span class="fw-bold">$<?php echo e($item->discount_value); ?></span></p>
                            <?php endif; ?>
                            <p class="card-text"><strong>Quantity:</strong> <?php echo e($item->quantity); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" id="printLabelBtn" class="btn btn-warning btn-sm">Print Label</button>
            <a href="<?php echo e(route('items.index')); ?>" class="btn btn-secondary btn-sm">Back to Items</a>
        </div>
    </div>
</div>


<!-- Print Quantity Modal -->
<div class="modal" id="printQuantityModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Print Labels</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="labelQuantity">Number of Labels to Print:</label>
                    <input type="number" class="form-control" id="labelQuantity" min="1" value="1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmPrint">Print</button>
            </div>
        </div>
    </div>
</div>


<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get the button and modal
    const printBtn = document.getElementById('printLabelBtn');
    const modal = document.getElementById('printQuantityModal');

    // Add click event to print button
    printBtn.addEventListener('click', function() {
        // Show the modal using Bootstrap's modal
        $('#printQuantityModal').modal('show');
    });

    // Handle the confirm print button
    document.getElementById('confirmPrint').addEventListener('click', function() {
        const quantity = document.getElementById('labelQuantity').value;

        // Show loading state
        this.disabled = true;
        this.textContent = 'Printing...';

        // Make the AJAX request
        fetch(`/items/<?php echo e($item->id); ?>/print-label`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Labels sent to printer successfully!');
            } else {
                alert('Failed to print labels: ' + data.error);
            }
            $('#printQuantityModal').modal('hide');
        })
        .catch(error => {
            alert('Error printing labels: ' + error);
        })
        .finally(() => {
            // Reset button state
            this.disabled = false;
            this.textContent = 'Print';
        });
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/items/show.blade.php ENDPATH**/ ?>