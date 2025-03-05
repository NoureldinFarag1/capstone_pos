@extends('layouts.dashboard')

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div
                class="bg-gradient-to-br from-red-600 via-red-500 to-red-700 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300">
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

        </div>



        <div class="grid grid-cols-1 md:grid-cols-1 gap-8">
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
                    <form action="{{ route('expenses.store') }}" method="POST" id="expenseForm"
                        class="transform transition-all duration-300 hover:scale-[1.01] space-y-6">
                        <div class="grid grid-cols-2 md:grid-cols-2 gap-8">
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
                                    <input type="text" name="reason" id="reason" list="reason-suggestions"
                                        class="pl-10 w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 @error('reason') border-red-500 @enderror"
                                        required placeholder="Enter expense reason...">
                                    <datalist id="reason-suggestions">
                                        @foreach($previousReasons as $reason)
                                            <option value="{{ $reason }}">
                                        @endforeach
                                    </datalist>
                                </div>
                                @error('reason')
                                    <p class="mt-1 text-sm text-red-500"><i
                                            class="fas fa-exclamation-circle mr-2"></i>{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <!-- ------------------------------------- -->
                        <button type="submit" id="submitExpenseBtn"
                            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-4 px-6 rounded-xl hover:from-blue-700 hover:to-blue-800 focus:ring-4 focus:ring-blue-200 font-medium transition-all duration-300 flex items-center justify-center space-x-3 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed">
                            <span class="flex items-center">
                                <i class="fas fa-plus-circle text-lg mr-2"></i>
                                <span class="normal-state">Add Expense</span>
                                <span class="loading-state hidden">
                                    <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing...
                                </span>
                            </span>
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
            <!-- Reason Totals Display -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-8">
                <div class="border-b border-gray-100 p-6 bg-gradient-to-r from-gray-50 to-white">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-gray-800">Expenses by Reason</h2>
                        <span class="px-4 py-1.5 text-sm font-medium text-indigo-700 bg-indigo-50 rounded-full">
                            <i class="fas fa-list mr-2"></i>Breakdown
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto rounded-xl">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gradient-to-r from-gray-50 to-gray-100">
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Reason
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Total Amount (EGP)
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($reasonTotals as $reasonTotal)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $reasonTotal->reason }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ number_format($reasonTotal->total, 2) }}</div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                            No expenses recorded.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Monthly Breakdown -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-8">
                <div class="border-b border-gray-100 p-6 bg-gradient-to-r from-gray-50 to-white">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-bold text-gray-800">Monthly Breakdown</h2>
                        <span class="px-4 py-1.5 text-sm font-medium text-emerald-700 bg-emerald-50 rounded-full">
                            <i class="fas fa-calendar-alt mr-2"></i>Monthly Summary
                        </span>
                    </div>
                    <div class="flex gap-4 items-center">
                        <select id="monthSelector"
                            class="rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            @foreach($monthlyBreakdown as $month)
                                <option value="{{ $month['month_name'] }}">{{ $month['month_name'] }}</option>
                            @endforeach
                        </select>
                        <button onclick="showMonthlyReasons()"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-filter mr-2"></i>Show Reasons
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto rounded-xl">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gradient-to-r from-gray-50 to-gray-100">
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Month
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Total Amount (EGP)
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Transactions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($monthlyBreakdown as $month)
                                    <tr class="hover:bg-gray-50 transition-all duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $month['month_name'] }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 font-semibold">
                                                {{ number_format($month['total_amount'], 2) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $month['transaction_count'] }}
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                            No monthly data available.
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
            document.getElementById('expenseForm').addEventListener('submit', function(e) {
                const button = document.getElementById('submitExpenseBtn');
                const normalState = button.querySelector('.normal-state');
                const loadingState = button.querySelector('.loading-state');
                
                button.disabled = true;
                normalState.classList.add('hidden');
                loadingState.classList.remove('hidden');
            });

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

            function showMonthlyReasons() {
                const selectedMonth = document.getElementById('monthSelector').value;

                Swal.fire({
                    title: `Expenses for ${selectedMonth}`,
                    html: '<div class="text-center">Loading...</div>',
                    showConfirmButton: false,
                    allowOutsideClick: true,
                    showCloseButton: true,
                    width: '600px'
                });

                fetch(`/expenses/monthly-reasons/${selectedMonth}`)
                    .then(response => response.json())
                    .then(data => {
                        let html = `
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead>
                                                <tr>
                                                    <th class="px-4 py-2 text-left">Reason</th>
                                                    <th class="px-4 py-2 text-right">Amount (EGP)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                `;

                        data.forEach(item => {
                            html += `
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-2 text-left">${item.reason}</td>
                                            <td class="px-4 py-2 text-right">${Number(item.total).toFixed(2)}</td>
                                        </tr>
                                    `;
                        });

                        html += '</tbody></table></div>';

                        Swal.update({
                            html: html
                        });
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!'
                        });
                    });
            }
        </script>
    @endpush
@endsection