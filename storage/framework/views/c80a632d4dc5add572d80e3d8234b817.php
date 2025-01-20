<?php $__env->startSection('content'); ?>
<div class="bg-white rounded-xl shadow-lg p-6">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Loyal Customers</h2>
            <p class="text-gray-600 mt-1">Customers who have made multiple purchases</p>
        </div>
        <div class="flex space-x-3">
            <span class="px-4 py-2 bg-purple-100 text-purple-600 rounded-lg text-sm font-semibold">
                <?php echo e($customers->total()); ?> Loyal Customers
            </span>
        </div>
    </div>

    <div class="overflow-x-auto bg-gray-50 rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Visits</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Spent</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Avg. Transaction</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Last Visit</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col">
                            <div class="text-sm font-medium text-gray-900"><?php echo e($customer->customer_name); ?></div>
                            <div class="text-sm text-gray-500"><?php echo e($customer->customer_phone); ?></div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <?php echo e($customer->visit_count); ?> visits
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <?php echo e(number_format($customer->total_spent, 2)); ?> EGP
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-900">
                            <?php echo e(number_format($customer->total_spent / $customer->visit_count, 2)); ?> EGP
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-900"><?php echo e(Carbon\Carbon::parse($customer->last_visit)->format('M d, Y')); ?></span>
                            <span class="text-xs text-gray-500"><?php echo e(Carbon\Carbon::parse($customer->last_visit)->diffForHumans()); ?></span>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <?php echo e($customers->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/sales/loyal-customers.blade.php ENDPATH**/ ?>