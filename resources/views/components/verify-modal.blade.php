<div x-show="showVerifyModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div class="relative bg-white rounded-lg max-w-md w-full p-6">
            <div class="mb-4">
                <h3 class="text-lg font-medium text-gray-900">Verify Access</h3>
                <p class="text-sm text-gray-600 mt-1">Please enter your password to view data</p>
            </div>

            <form @submit.prevent="verifyAccess">
                <div class="mb-4">
                    <input type="password"
                           x-model="password"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Enter your password">
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button"
                            @click="showVerifyModal = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                        Verify
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
