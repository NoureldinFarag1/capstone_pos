@extends('layouts.dashboard')

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <div
                class="bg-gradient-to-br from-blue-600 via-blue-500 to-blue-700 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Today's Expenses</h3>
                    <div class="w-12 h-12 rounded-xl bg-white/25 backdrop-blur-sm flex items-center justify-center">
                        <i class="fas fa-receipt text-2xl"></i>
                    </div>
                </div>
                <div class="text-4xl font-bold mb-3">{{ number_format($todayTotal ?? 0, 2) }} EGP</div>
                @if(isset($todayCount))
                    <div class="text-sm bg-white/30 backdrop-blur-sm rounded-lg px-4 py-2 inline-block">
                        <i class="fas fa-chart-bar mr-2"></i>{{ $todayCount }} transactions today
                    </div>
                @endif
            </div>

            <div
                class="bg-gradient-to-br from-green-600 via-green-500 to-green-700 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Monthly Expenses</h3>
                    <div class="w-12 h-12 rounded-xl bg-white/25 backdrop-blur-sm flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-2xl"></i>
                    </div>
                </div>
                <div class="text-4xl font-bold mb-3">{{ number_format($monthlyTotal ?? 0, 2) }} EGP</div>
                @if(isset($monthlyCount))
                    <div class="text-sm bg-white/30 backdrop-blur-sm rounded-lg px-4 py-2 inline-block">
                        <i class="fas fa-chart-bar mr-2"></i>{{ $monthlyCount }} transactions this month
                    </div>
                @endif
            </div>

            <div
                class="bg-gradient-to-br from-purple-600 via-purple-500 to-purple-700 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Average Expense</h3>
                    <div class="w-12 h-12 rounded-xl bg-white/25 backdrop-blur-sm flex items-center justify-center">
                        <i class="fas fa-chart-line text-2xl"></i>
                    </div>
                </div>
                <div class="text-4xl font-bold mb-3">{{ number_format($averageExpense ?? 0, 2) }} EGP</div>
                <div class="text-sm bg-white/30 backdrop-blur-sm rounded-lg px-4 py-2 inline-block">
                    Based on all transactions
                </div>
            </div>
        </div>

        <!-- Reason Totals Chart -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-8" x-data="{ isOpen: false }">
            <div class="border-b border-gray-100 p-6 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-800">Monthly Expenses by Reason</h2>
                    <span class="px-4 py-1.5 text-sm font-medium text-indigo-700 bg-indigo-50 rounded-full">
                        <i class="fas fa-chart-pie mr-2"></i>Analysis
                    </span>
                    <button @click="isOpen = !isOpen" class="text-gray-400 hover:text-gray-600">
                        <i class="fas" :class="isOpen ? 'fa-eye' : 'fa-eye-slash'"></i>
                    </button>
                </div>
            </div>
            <div class="p-6" x-show="isOpen" x-transition>
                <div class="h-[400px]">
                    <canvas id="reasonChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Expense Form -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="border-b border-gray-100 p-6 bg-gradient-to-r from-gray-50 to-white">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-gray-800">Add New Expense</h2>
                        <span class="px-4 py-1.5 text-sm font-medium text-blue-700 bg-blue-50 rounded-full">
                            <i class="fas fa-plus-circle mr-2"></i>New Entry
                        </span>
                    </div>
                </div>
                <div class="p-8">
                    <form action="{{ route('expenses.store') }}" method="POST"
                        class="transform transition-all duration-300 hover:scale-[1.01] space-y-6">
                        @csrf
                        <div class="space-y-2">
                            <label for="amount" class="block text-sm font-medium text-gray-700">Amount (EGP)</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500">
                                    </span>
                                </div>
                                <input type="number" step="0.01" name="amount" id="amount"
                                    class="pl-10 w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 @error('amount') border-red-500 @enderror"
                                    required placeholder="Enter amount...">
                            </div>
                            @error('amount')
                                <p class="mt-1 text-sm text-red-500"><i
                                        class="fas fa-exclamation-circle mr-2"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="reason" class="block text-sm font-medium text-gray-700">Reason</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">

                                </div>
                                <input type="text" name="reason" id="reason" rows="3"
                                    class="pl-10 w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 @error('reason') border-red-500 @enderror"
                                    required placeholder="Enter expense reason...">
                            </div>
                            @error('reason')
                                <p class="mt-1 text-sm text-red-500"><i
                                        class="fas fa-exclamation-circle mr-2"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-4 px-6 rounded-xl hover:from-blue-700 hover:to-blue-800 focus:ring-4 focus:ring-blue-200 font-medium transition-all duration-300 flex items-center justify-center space-x-3 shadow-lg hover:shadow-xl">
                            <i class="fas fa-plus-circle text-lg"></i>
                            <span>Add Expense</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Expenses List -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="border-b border-gray-100 p-6 bg-gradient-to-r from-gray-50 to-white">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-gray-800">Recent Expenses</h2>
                        <span class="px-4 py-1.5 text-sm font-medium text-purple-700 bg-purple-50 rounded-full">
                            <i class="fas fa-history mr-2"></i>Transaction History
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto rounded-xl">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gradient-to-r from-gray-50 to-gray-100">
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Amount
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Reason
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($expenses as $expense)
                                    <tr id="expense-{{ $expense->id }}"
                                        class="hover:bg-gray-50 transition-all duration-200 group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 font-medium">
                                                {{ $expense->created_at->format('M d, Y') }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $expense->created_at->format('h:i A') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <span class="amount-display font-semibold text-blue-600">
                                                {{ number_format($expense->amount, 2) }} EGP
                                            </span>
                                            <input type="number" step="0.01"
                                                class="amount-input hidden w-full border rounded px-2 py-1"
                                                value="{{ $expense->amount }}">
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <span class="reason-display">{{ $expense->reason }}</span>
                                            <input type="text" class="reason-input hidden w-full border rounded px-2 py-1"
                                                value="{{ $expense->reason }}">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-3">
                                                <form action="{{ route('expenses.destroy', $expense) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    @role('admin|moderator')
                                                    @method('DELETE')
                                                    <button type="button" onclick="confirmDelete(this)"
                                                        class="text-red-600 hover:text-red-900 transition-colors duration-200">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    @endrole
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-16 text-center">
                                            <div class="flex flex-col items-center justify-center text-gray-400">
                                                <i class="fas fa-receipt text-5xl mb-4"></i>
                                                <p class="text-xl font-medium mb-2">No expenses recorded yet</p>
                                                <p class="text-sm">Start tracking your expenses by adding your first entry</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function toggleEdit(id) {
                const row = document.getElementById(`expense-${id}`);
                row.querySelector('.amount-display').classList.toggle('hidden');
                row.querySelector('.amount-input').classList.toggle('hidden');
                row.querySelector('.reason-display').classList.toggle('hidden');
                row.querySelector('.reason-input').classList.toggle('hidden');
                row.querySelector('.edit-btn').classList.toggle('hidden');
                row.querySelector('.save-btn').classList.toggle('hidden');
            }

            function saveEdit(id) {
                const row = document.getElementById(`expense-${id}`);
                const amount = row.querySelector('.amount-input').value;
                const reason = row.querySelector('.reason-input').value;

                fetch(`/expenses/${id}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        amount,
                        reason
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            row.querySelector('.amount-display').textContent = `${parseFloat(amount).toFixed(2)} EGP`;
                            row.querySelector('.reason-display').textContent = reason;
                            toggleEdit(id);
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Expense updated successfully!',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!'
                        });
                    });
            }

            function confirmDelete(button) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        button.closest('form').submit();
                    }
                });
            }

            // Add Chart initialization
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('reasonChart').getContext('2d');
                const reasonTotals = @json($reasonTotals);

                const chartData = {
                    labels: reasonTotals.map(item => item.reason),
                    datasets: [{
                        label: 'Total Amount (EGP)',
                        data: reasonTotals.map(item => item.total),
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.7)', // Blue
                            'rgba(16, 185, 129, 0.7)', // Green
                            'rgba(139, 92, 246, 0.7)', // Purple
                            'rgba(239, 68, 68, 0.7)', // Red
                            'rgba(245, 158, 11, 0.7)', // Yellow
                            'rgba(107, 114, 128, 0.7)', // Gray
                            'rgba(206, 147, 216, 0.7)', // Light Purple
                            'rgba(85, 172, 238, 0.7)', // Light Blue
                            'rgba(255, 193, 7, 0.7)', // Amber
                            'rgba(147, 197, 114, 0.7)' // Light Green
                        ],
                        borderColor: [
                            'rgba(59, 130, 246, 1)',
                            'rgba(16, 185, 129, 1)',
                            'rgba(139, 92, 246, 1)',
                            'rgba(239, 68, 68, 1)',
                            'rgba(245, 158, 11, 1)',
                            'rgba(107, 114, 128, 1)',
                            'rgba(206, 147, 216, 1)',
                            'rgba(85, 172, 238, 1)',
                            'rgba(255, 193, 7, 1)',
                            'rgba(147, 197, 114, 1)'
                        ],
                        borderWidth: 1
                    }]
                };

                new Chart(ctx, {
                    type: 'doughnut', // Changed to doughnut chart
                    data: chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right', // Display legend on the right
                                align: 'center',
                                labels: {
                                    boxWidth: 20,
                                    padding: 20,
                                    font: {
                                        size: 14
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = chartData.datasets[0].data.reduce((acc, cur) => acc + cur,
                                            0);
                                        const percentage = total > 0 ? (value / total * 100).toFixed(2) + '%' :
                                            '0.00%';
                                        return `${label}: ${value.toFixed(2)} EGP (${percentage})`;
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection