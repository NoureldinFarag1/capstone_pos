<?php $__env->startSection('content'); ?>
<div class="bg-white rounded-xl shadow-lg p-6">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800"><?php echo e($periodLabel); ?> <?php echo e($methodLabel); ?> Sales</h2>
            <p class="text-gray-600 mt-1">Detailed transaction history</p>
        </div>
        <div class="flex items-center space-x-3">
            <span class="px-4 py-2 bg-blue-100 text-blue-600 rounded-lg text-sm font-semibold">
                <?php echo e($sales->total()); ?> Transactions
            </span>
            <span class="px-4 py-2 bg-green-100 text-green-600 rounded-lg text-sm font-semibold">
                Total: <?php echo e(number_format($sales->sum('total_amount'), 2)); ?> EGP
            </span>
        </div>
    </div>

    <div class="overflow-x-auto bg-gray-50 rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Sale ID</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Time</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-medium text-blue-600">#<?php echo e(str_pad($sale->id, 4, '0', STR_PAD_LEFT)); ?></span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col">
                            <div class="text-sm font-medium text-gray-900"><?php echo e($sale->customer_name ?: 'Walk-in Customer'); ?></div>
                            <?php if($sale->customer_phone): ?>
                                <div class="text-sm text-gray-500"><?php echo e($sale->customer_phone); ?></div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <?php echo e(number_format($sale->total_amount, 2)); ?> EGP
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div class="flex flex-col">
                            <span><?php echo e($sale->created_at->format('M d, Y')); ?></span>
                            <span class="text-xs text-gray-400"><?php echo e($sale->created_at->format('h:i A')); ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="<?php echo e(route('sales.show', $sale->id)); ?>"
                           class="inline-flex items-center px-3 py-1 border border-blue-600 rounded-md text-blue-600 bg-white hover:bg-blue-50 transition-colors">
                            <i class="fas fa-eye mr-2"></i> View Details
                        </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <?php echo e($sales->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/sales/payment-method.blade.php ENDPATH**/ ?>