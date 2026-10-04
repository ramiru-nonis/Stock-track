<div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b border-gray-200 dark:border-gray-700 pb-5">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Product Categories</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Organize products by shop department and category.</p>
        </div>
        @can('create', App\Models\Category::class)
            <div class="mt-4 md:mt-0">
                <button wire:click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Category
                </button>
            </div>
        @endcan
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
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search categories..."
            class="w-full md:w-1/3 px-4 py-2 bg-gray-50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white">
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
            <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs font-bold text-gray-500 uppercase">
                <tr>
                    <th class="px-6 py-3">Category Name</th>
                    <th class="px-6 py-3">Description</th>
                    <th class="px-6 py-3">Total Products</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($categories as $category)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                        <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ $category->name }}</td>
                        <td class="px-6 py-4 text-xs text-gray-500 dark:text-gray-400">{{ $category->description ?? 'No description' }}</td>
                        <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ $category->products_count }}</td>
                        <td class="px-6 py-4 text-right space-x-2">
                            @can('update', $category)
                                <button wire:click="openEditModal({{ $category->id }})" class="text-indigo-600 dark:text-indigo-400 font-semibold text-xs">Edit</button>
                            @endcan
                            @can('delete', $category)
                                <button wire:click="confirmDelete({{ $category->id }})" class="text-rose-600 dark:text-rose-400 font-semibold text-xs">Delete</button>
                            @endcan
                            @cannot('update', $category)
                                <span class="text-xs text-gray-400">View Only</span>
                            @endcannot
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-400">No categories found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
            {{ $categories->links() }}
        </div>
    </div>

    <!-- Category Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md p-6 space-y-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $editingCategoryId ? 'Edit Category' : 'Create Category' }}</h3>
                <form wire:submit.prevent="saveCategory" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Category Name</label>
                        <input type="text" wire:model="name" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:text-white border-gray-300 dark:border-gray-600">
                        @error('name') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Description</label>
                        <textarea wire:model="description" rows="3" class="w-full px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:text-white border-gray-300 dark:border-gray-600"></textarea>
                        @error('description') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg shadow">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md p-6 space-y-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Confirm Deletion</h3>
                <p class="text-sm text-gray-600 dark:text-gray-300">Are you sure you want to delete this category?</p>
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button wire:click="$set('showDeleteModal', false)" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">Cancel</button>
                    <button wire:click="deleteCategory" class="px-4 py-2 bg-rose-600 text-white text-sm font-medium rounded-lg shadow">Delete</button>
                </div>
            </div>
        </div>
    @endif
</div>
