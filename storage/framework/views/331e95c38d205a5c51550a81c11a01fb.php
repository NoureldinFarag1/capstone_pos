<?php $__env->startSection('content'); ?>
<div class="container mx-auto py-6 px-4">
    <!-- Title Section with improved contrast -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 tracking-tight">Sales Dashboard</h1>
        <a href="<?php echo e(route('sales.create')); ?>" class="w-full md:w-auto bg-blue-600 text-white px-6 py-3 rounded-lg shadow-lg hover:bg-blue-700 transition-all flex items-center justify-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>New Sale</span>
        </a>
    </div>

    <!-- Enhanced Success Message -->
    <?php if(session('success')): ?>
        <div class="alert alert-success p-4 mb-6 text-green-800 bg-green-100 border-l-4 border-green-500 rounded-md shadow-md animate-fadeOut">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <?php echo e(session('success')); ?>

            </div>
        </div>
    <?php endif; ?>

    <!-- Improved Action Bar -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <!-- Enhanced Search Form -->
            <form method="GET" action="<?php echo e(route('sales.index')); ?>" class="flex-1">
                <div class="flex flex-col md:flex-row gap-3">
                    <div class="flex-1">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Transaction</label>
                        <div class="relative">
                            <input
                                id="search"
                                type="text"
                                name="search"
                                value="<?php echo e(request('search')); ?>"
                                placeholder="Enter Transaction ID or Daily ID..."
                                class="w-full border-gray-300 rounded-lg pl-10 pr-4 py-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="mt-auto bg-blue-600 text-white px-6 py-2 rounded-lg shadow hover:bg-blue-700 transition-colors">
                        Search
                    </button>
                </div>
            </form>

            <!-- Enhanced Filter & Export -->
            <form action="<?php echo e(route('items.exportCSV')); ?>" method="POST" class="flex flex-col md:flex-row gap-3">
                <?php echo csrf_field(); ?>
                <div>
                    <label for="brand" class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
                    <select id="brand" name="brand_id" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Brands</option>
                        <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($brand->id); ?>"><?php echo e($brand->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" />
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" id="end_date" name="end_date" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" />
                </div>
                <button type="submit" class="mt-auto bg-green-600 text-white px-6 py-2 rounded-lg shadow hover:bg-green-700 transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export CSV
                </button>
            </form>
        </div>
    </div>

    <!-- Enhanced Sales Table -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issued by</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    <?php $__empty_1 = true; $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 <?php echo e(request('search') == $sale->id || request('search') == $sale->display_id ? 'bg-yellow-50' : ''); ?>">
                        <td class="px-6 py-4 text-gray-800">
                            #<?php echo e($sale->id); ?>

                            <span class="text-sm text-gray-500">
                                (<?php echo e($sale->sale_date->format('d/m')); ?> - #<?php echo e(str_pad($sale->display_id, 4, '0', STR_PAD_LEFT)); ?>)
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-800">$<?php echo e(number_format($sale->total_amount, 2)); ?></td>
                        <td class="px-6 py-4 text-gray-800"><?php echo e($sale->created_at->format('Y-m-d H:i')); ?></td>
                        <td class="px-6 py-4"><?php echo e($sale->user ? $sale->user->name : 'Unknown User'); ?></td>
                        <td class="px-6 py-4">
                            <?php switch($sale->refund_status):
                                case ('no_refund'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        No Refund
                                    </span>
                                    <?php break; ?>
                                <?php case ('partial_refund'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        Partially Refunded
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
                                        Refund
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
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="mt-2 text-sm font-medium">No sales records found</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Enhanced Pagination -->
    <div class="mt-6">
        <?php echo e($sales->appends(request()->query())->links('pagination::tailwind')); ?>

    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
    .animate-fadeOut {
        animation: fadeOut 5s forwards;
    }
    @keyframes fadeOut {
        0% { opacity: 1; }
        70% { opacity: 1; }
        100% { opacity: 0; display: none; }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('form[onsubmit]').forEach(form => {
        form.onsubmit = function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you absolutely sure?',
                text: "This action is cannot be undone and will permanently delete the sale!",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        };
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/sales/index.blade.php ENDPATH**/ ?>