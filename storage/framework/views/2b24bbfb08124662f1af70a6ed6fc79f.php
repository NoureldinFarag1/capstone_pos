<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="<?php echo e(route('items.index')); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Items
        </a>
    </div>

    <div class="row">
        <!-- Parent Item Details -->
        <div class="col-md-4">
            <div class="card mb-3 shadow-sm">
                <div class="d-flex justify-content-between p-3 border-bottom">
                    <h5 class="card-header-title mb-0">Item Details</h5>
                    <a href="<?php echo e(route('items.edit', $item->id)); ?>" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </div>
                <div class="item-image-container">
                    <img src="<?php echo e(asset('storage/' . $item->picture)); ?>" alt="<?php echo e($item->name); ?>"
                        class="card-img-top item-image">
                </div>
                <div class="card-body">
                    <h4 class="card-title text-center mb-3"><?php echo e($item->name); ?></h4>
                    <div class="item-metadata mb-3">
                        <span class="badge bg-secondary">ID: <?php echo e($item->id); ?></span>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <?php if($item->barcode): ?>
                                <img src="<?php echo e(asset('storage/' . $item->barcode)); ?>" alt="Barcode" class="img-fluid">
                                <p class="card-text">Barcode: <?php echo e($item->code); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-6">
                            <p class="card-text"><strong>Brand:</strong> <?php echo e($item->brand->name); ?></p>
                            <p class="card-text"><strong>Category:</strong> <?php echo e($item->category->name); ?></p>
                            <p class="card-text"><strong>Base Price:</strong> EGP<?php echo e($item->selling_price); ?></p>
                            <?php if($item->discount_type === 'percentage'): ?>
                                <p class="mb-1 text-muted">Sale: <span class="fw-bold"><?php echo e($item->discount_value); ?>%</span>
                                </p>
                            <?php else: ?>
                                <p class="mb-1 text-muted">Sale: <span class="fw-bold">EGP<?php echo e($item->discount_value); ?></span>
                                </p>
                            <?php endif; ?>
                            <p class="card-text"><strong>Selling Price:</strong> EGP<?php echo e($item->priceAfterSale()); ?></p>
                            <p class="card-text"><strong>Total Stock:</strong> <?php echo e($item->quantity); ?></p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge <?php echo e($item->quantity > 0 ? 'bg-success' : 'bg-danger'); ?> badge-lg">
                            <?php echo e($item->quantity > 0 ? 'In Stock' : 'Out of Stock'); ?>

                        </span>
                        <span class="text-muted small">Last updated: <?php echo e($item->updated_at->diffForHumans()); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Variants Table -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Item Variants</h5>
                        <button type="button" class="btn btn-primary btn-sm" id="saveAllQuantities">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Variant</th>
                                    <th>Size</th>
                                    <th>Color</th>
                                    <th class="text-center">Stock</th>
                                    <th>Barcode</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $item->variants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr data-variant-id="<?php echo e($variant->id); ?>" class="variant-row">
                                        <td><?php echo e($variant->name); ?></td>
                                        <td><?php echo e($variant->sizes->first()->name ?? '-'); ?></td>
                                        <td>
                                            <?php if($variant->colors->first()): ?>
                                                <span class="d-flex align-items-center gap-2">
                                                    <span class="color-preview rounded-circle"
                                                        style="width: 15px; height: 15px; background-color: <?php echo e($variant->colors->first()->hex_code); ?>;"></span>
                                                    <?php echo e($variant->colors->first()->name); ?>

                                                </span>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center gap-2">
                                                <input type="number" class="form-control form-control-sm quantity-input"
                                                    value="<?php echo e($variant->quantity); ?>" min="0" style="width: 80px;">
                                                <span class="stock-status">
                                                    <?php if($variant->quantity == 0): ?>
                                                        <span class="badge bg-danger">Out of Stock</span>
                                                    <?php elseif($variant->quantity <= 5): ?> <span class="badge bg-warning">Low
                                                        Stock</span>
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if($variant->barcode): ?>
                                                <img src="<?php echo e(asset('storage/' . $variant->barcode)); ?>" alt="Barcode"
                                                    class="img-fluid" style="max-height: 30px">
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-warning btn-sm print-label"
                                                data-variant-id="<?php echo e($variant->id); ?>">
                                                Print Label
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
    <style>
        .item-metadata {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }

            .btn-sm {
                padding: 0.25rem 0.5rem;
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Get all print buttons
            const printBtns = document.querySelectorAll('.print-label');

            // Add click event to all print buttons
            printBtns.forEach(btn => {
                btn.addEventListener('click', function () {
                    const variantId = this.dataset.variantId;

                    // Show SweetAlert input for quantity
                    Swal.fire({
                        title: 'Print Labels',
                        input: 'number',
                        inputLabel: 'Number of Labels to Print:',
                        inputAttributes: {
                            min: 1,
                            value: 1
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Print',
                        showLoaderOnConfirm: true,
                        preConfirm: (quantity) => {
                            return fetch(`/items/${variantId}/print-label`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                                },
                                body: JSON.stringify({
                                    quantity: quantity
                                })
                            })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(response.statusText);
                                    }
                                    return response.json();
                                })
                                .catch(error => {
                                    Swal.showValidationMessage(
                                        `Request failed: ${error}`);
                                });
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        if (result.isConfirmed) {
                            if (result.value.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: 'Labels sent to printer successfully!'
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Failed to print labels: ' + result.value
                                        .error
                                });
                            }
                        }
                    });
                });
            });

            // Handle quantity updates
            const saveAllBtn = document.getElementById('saveAllQuantities');

            saveAllBtn.addEventListener('click', function () {
                // Show loading state
                saveAllBtn.disabled = true;
                saveAllBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';

                const updates = [];
                document.querySelectorAll('tr[data-variant-id]').forEach(row => {
                    updates.push({
                        id: row.dataset.variantId,
                        quantity: parseInt(row.querySelector('.quantity-input').value) || 0
                    });
                });

                // Send updates to server
                fetch('/items/update-variants-quantity', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        updates: updates
                    })
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Quantities updated successfully!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        } else {
                            throw new Error(data.error || 'Failed to update quantities');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error updating quantities: ' + error.message
                        });
                    })
                    .finally(() => {
                        // Reset button state
                        saveAllBtn.disabled = false;
                        saveAllBtn.textContent = 'Save All Changes';
                    });
            });

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });

        function exportVariants() {
            // Add export functionality here
            alert('Export feature coming soon!');
        }
    </script>
<?php $__env->stopPush(); ?>

<style>
    .color-preview {
        display: inline-block;
        border: 1px solid #dee2e6;
    }

    .table th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .quantity-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
        border-color: #80bdff;
    }

    @media print {

        .btn-group,
        .print-label {
            display: none;
        }
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\LocalHub POS\capstone_pos\resources\views/items/show.blade.php ENDPATH**/ ?>