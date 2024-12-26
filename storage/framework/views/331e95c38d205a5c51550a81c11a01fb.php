<?php $__env->startSection('content'); ?>
<div class="container mx-auto py-6 px-4">
    <!-- Title Section -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800">Sales Management</h1>
        <a href="<?php echo e(route('sales.create')); ?>" class="bg-blue-600 text-white px-5 py-3 rounded-md shadow hover:bg-blue-700 transition">
            + Create New Sale
        </a>
    </div>

    <!-- Success Message -->
    <?php if(session('success')): ?>
        <div class="alert alert-success p-4 mb-6 text-green-800 bg-green-200 rounded-md shadow-md">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <!-- Action Bar -->
    <div class="bg-gray-50 p-6 rounded-md shadow-md mb-6">
        <div class="flex flex-wrap gap-4 items-center">
            <!-- Search Form -->
            <form method="GET" action="<?php echo e(route('sales.index')); ?>" class="flex flex-1 items-center gap-3">
                <input
                    type="text"
                    name="search"
                    value="<?php echo e(request('search')); ?>"
                    placeholder="Search by Transaction ID"
                    class="w-full md:w-80 border-gray-300 rounded-md p-2 text-sm focus:ring-blue-500 focus:border-blue-500"
                />
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md shadow hover:bg-blue-700">
                    Search
                </button>
            </form>

            <!-- Filter & Export -->
            <form action="<?php echo e(route('items.exportCSV')); ?>" method="POST" class="flex items-center gap-3">
                <?php echo csrf_field(); ?>
                <select name="brand_id" class="border-gray-300 rounded-md p-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Brands</option>
                    <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($brand->id); ?>"><?php echo e($brand->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <input type="date" name="start_date" class="border-gray-300 rounded-md p-2 text-sm focus:ring-blue-500 focus:border-blue-500" />
                <input type="date" name="end_date" class="border-gray-300 rounded-md p-2 text-sm focus:ring-blue-500 focus:border-blue-500" />
                <button type="submit" class="bg-green-700 text-white px-4 py-2 rounded-md shadow hover:bg-green-800">
                    Export CSV
                </button>
            </form>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="bg-white shadow rounded-md overflow-x-auto">
        <table class="min-w-full table-auto divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">ID</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Total Amount</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Date</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Actions</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Issued by</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php $__empty_1 = true; $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="<?php echo e(request('search') == $sale->id ? 'bg-yellow-100' : ''); ?>">
                    <td class="px-6 py-4 text-gray-800"><?php echo e($sale->id); ?></td>
                    <td class="px-6 py-4 text-gray-800">$<?php echo e(number_format($sale->total_amount, 2)); ?></td>
                    <td class="px-6 py-4 text-gray-800"><?php echo e($sale->created_at->format('Y-m-d H:i')); ?></td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <!-- View Button -->
                            <a href="<?php echo e(route('sales.show', $sale->id)); ?>" class="bg-blue-600 text-white px-4 py-2 rounded-md shadow hover:bg-blue-700">
                                View
                            </a>

                            <!-- Refund Button -->
                            <?php if($sale->refund_status === 'no_refund'): ?>
                                <a href="<?php echo e(route('refund.create', $sale->id)); ?>" class="bg-yellow-500 text-white px-4 py-2 rounded-md shadow hover:bg-yellow-600">
                                    Refund
                                </a>
                            <?php elseif($sale->refund_status === 'partial_refund'): ?>
                                <a href="<?php echo e(route('refund.create', $sale->id)); ?>" class="bg-yellow-500 text-white px-4 py-2 rounded-md shadow hover:bg-yellow-600">
                                    Partial Refund
                                </a>
                            <?php endif; ?>

                            <!-- Delete Button (Admin Only) -->
                            <?php if (\Illuminate\Support\Facades\Blade::check('role', 'admin|moderator')): ?>
                                <form action="<?php echo e(route('sales.destroy', $sale->id)); ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this sale?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md shadow hover:bg-red-700">
                                        Delete
                                    </button>
                                </form>
                            <?php endif; ?>
                            <td><?php echo e($sale->user ? $sale->user->name : 'Unknown User'); ?></td>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <?php switch($sale->refund_status):
                            case ('no_refund'): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                    Active
                                </span>
                                <?php break; ?>
                            <?php case ('partial_refund'): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                    Partial Refund
                                </span>
                                <?php break; ?>
                            <?php case ('full_refund'): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                    Refunded
                                </span>
                                <?php break; ?>
                            <?php default: ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                    Unknown
                                </span>
                        <?php endswitch; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="4" class="text-center py-6 text-gray-500">
                        No sales records found.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        <?php echo e($sales->appends(request()->query())->links('pagination::tailwind')); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/sales/index.blade.php ENDPATH**/ ?>