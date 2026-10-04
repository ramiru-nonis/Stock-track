<div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="border-b border-gray-200 dark:border-gray-700 pb-5 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                <svg class="w-7 h-7 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Staff Account Management (Owner Administration)
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Create staff credentials, toggle active status, or safely deactivate accounts while preserving historical audit logs.</p>
        </div>
        <button wire:click="openCreateModal" class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Add New Staff Account
        </button>
    </div>

    @if(session()->has('message'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-900/30 border-l-4 border-emerald-500 text-emerald-700 dark:text-emerald-300 text-sm rounded-r-lg shadow-sm">
            {{ session('message') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="p-4 bg-rose-50 dark:bg-rose-900/30 border-l-4 border-rose-500 text-rose-700 dark:text-rose-300 text-sm rounded-r-lg shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search staff by name or email..."
            class="w-full md:w-1/3 px-4 py-2 bg-gray-50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white">
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
            <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs font-bold text-gray-500 uppercase">
                <tr>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">Role</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Registered Date</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($staffMembers as $staff)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                        <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ $staff->name }}</td>
                        <td class="px-6 py-4 text-xs font-mono">{{ $staff->email }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">
                                STAFF
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($staff->trashed() || !$staff->is_active)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-900/50 dark:text-rose-300">
                                    Deactivated
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">
                                    Active
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-500">{{ $staff->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right space-x-2">
                            @if(!$staff->trashed())
                                <button wire:click="toggleUserStatus({{ $staff->id }})" class="text-xs font-semibold {{ $staff->is_active ? 'text-amber-600' : 'text-emerald-600' }}">
                                    {{ $staff->is_active ? 'Disable' : 'Enable' }}
                                </button>
                                <button wire:click="confirmDeactivate({{ $staff->id }})" class="text-xs font-semibold text-rose-600 dark:text-rose-400">
                                    Deactivate Account
                                </button>
                            @else
                                <span class="text-xs text-gray-400 italic">Deactivated (Audit Intact)</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-400">No staff accounts found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
            {{ $staffMembers->links() }}
        </div>
    </div>

    <!-- Create Staff Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md p-6 space-y-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Create Staff Account</h3>
                <form wire:submit.prevent="createStaff" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Staff Name</label>
                        <input type="text" wire:model="name" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:text-white border-gray-300 dark:border-gray-600">
                        @error('name') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Email Address</label>
                        <input type="email" wire:model="email" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:text-white border-gray-300 dark:border-gray-600">
                        @error('email') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Password</label>
                        <input type="password" wire:model="password" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:text-white border-gray-300 dark:border-gray-600">
                        @error('password') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Confirm Password</label>
                        <input type="password" wire:model="password_confirmation" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:text-white border-gray-300 dark:border-gray-600">
                    </div>
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg text-xs text-indigo-800 dark:text-indigo-300">
                        Account will automatically be assigned the <strong>STAFF</strong> role.
                    </div>
                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg shadow">Create Staff</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Deactivate Confirmation Modal -->
    @if($showDeactivateModal)
        <div class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md p-6 space-y-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Confirm Deactivation</h3>
                <p class="text-sm text-gray-600 dark:text-gray-300">Are you sure you want to deactivate this staff account? The user will no longer be able to log in, but all historical stock movements logged under their name will be preserved in the audit history.</p>
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button wire:click="$set('showDeactivateModal', false)" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">Cancel</button>
                    <button wire:click="deactivateStaff" class="px-4 py-2 bg-rose-600 text-white text-sm font-medium rounded-lg shadow">Deactivate Account</button>
                </div>
            </div>
        </div>
    @endif
</div>
