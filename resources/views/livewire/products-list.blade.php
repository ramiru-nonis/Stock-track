<div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b border-gray-200 dark:border-gray-700 pb-5">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">Product Inventory</h1>
            <p class="text-sm text-gray-600 dark:text-slate-300 mt-1">Manage shop products, database-stored images, pricing in LKR & USD live conversion.</p>
        </div>
        @can('create', App\Models\Product::class)
            <div class="mt-4 md:mt-0">
                <button wire:click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Product
                </button>
            </div>
        @endcan
    </div>

    <!-- Flash Message -->
    @if(session()->has('message'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-900/40 border-l-4 border-emerald-500 text-emerald-800 dark:text-emerald-200 text-sm rounded-r-lg shadow-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    <!-- Filters & Search -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 flex flex-col md:flex-row gap-4 justify-between items-center">
        <div class="w-full md:w-1/3 relative">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name, SKU or category..."
                class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-gray-700/80 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-slate-100 placeholder-gray-500 dark:placeholder-slate-400 focus:ring-indigo-500 focus:border-indigo-500">
            <svg class="w-4 h-4 absolute left-3 top-3 text-gray-400 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>

        <div class="w-full md:w-1/4">
            <select wire:model.live="categoryFilter" class="w-full py-2 bg-gray-50 dark:bg-gray-700/80 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-slate-100 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="" class="dark:bg-gray-800 dark:text-slate-100">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" class="dark:bg-gray-800 dark:text-slate-100">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700 dark:text-slate-200">
                <thead class="bg-gray-50 dark:bg-gray-700/80 text-xs font-bold text-gray-700 dark:text-slate-200 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-3">Image</th>
                        <th class="px-6 py-3">SKU</th>
                        <th class="px-6 py-3">Product Name</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Price (LKR / USD)</th>
                        <th class="px-6 py-3">Stock Quantity</th>
                        <th class="px-6 py-3">Low Threshold</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/80">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/40 transition">
                            <td class="px-6 py-4">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-12 h-12 rounded-lg object-cover border border-gray-200 dark:border-gray-700 shadow-sm">
                                @else
                                    <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400 dark:text-slate-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-gray-900 dark:text-slate-100">
                                {{ $product->sku }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-900 dark:text-slate-100">{{ $product->name }}</span>
                                @if($product->description)
                                    <span class="block text-xs text-gray-500 dark:text-slate-300 truncate max-w-xs">{{ $product->description }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-slate-200 rounded-md text-xs font-medium border border-gray-200 dark:border-gray-600">
                                    {{ $product->category?->name ?? 'Uncategorized' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 dark:text-slate-100">
                                    LKR {{ number_format($product->selling_price, 2) }}
                                </div>
                                <div class="text-xs text-indigo-600 dark:text-indigo-300 font-semibold">
                                    @if($usdRate)
                                        &approx; USD ${{ number_format($product->selling_price * $usdRate, 2) }}
                                    @else
                                        USD conversion N/A
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($product->isLowStock())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-900 dark:bg-amber-900/80 dark:text-amber-200 border border-amber-200 dark:border-amber-700">
                                        {{ $product->quantity }} (Low Stock)
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-900 dark:bg-emerald-900/80 dark:text-emerald-200 border border-emerald-200 dark:border-emerald-700">
                                        {{ $product->quantity }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-gray-700 dark:text-slate-300">
                                {{ $product->low_stock_threshold }}
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                @can('update', $product)
                                    <button wire:click="openEditModal({{ $product->id }})" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 font-bold text-xs">Edit</button>
                                @endcan
                                @can('delete', $product)
                                    <button wire:click="confirmDelete({{ $product->id }})" class="text-rose-600 dark:text-rose-400 hover:text-rose-900 dark:hover:text-rose-300 font-bold text-xs">Delete</button>
                                @endcan
                                @cannot('update', $product)
                                    <span class="text-xs text-gray-500 dark:text-slate-400">View Only</span>
                                @endcannot
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-slate-300">
                                No products found matching your search criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
            {{ $products->links() }}
        </div>
    </div>

    <!-- Product Create/Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 w-full max-w-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-slate-100">
                        {{ $editingProductId ? 'Edit Product' : 'Create New Product' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-white">&times;</button>
                </div>
                
                <form wire:submit.prevent="saveProduct" class="p-6 space-y-4">
                    <!-- Image Upload Input & Preview -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-slate-200 uppercase mb-1">Product Image (Saved directly in MySQL Database)</label>
                        
                        <div class="flex items-center space-x-4 mt-2">
                            @if ($image)
                                <img src="{{ $image->temporaryUrl() }}" class="w-16 h-16 rounded-xl object-cover border border-gray-300 dark:border-gray-600 shadow-sm">
                            @elseif ($existingImageUrl)
                                <img src="{{ $existingImageUrl }}" class="w-16 h-16 rounded-xl object-cover border border-gray-300 dark:border-gray-600 shadow-sm">
                            @else
                                <div class="w-16 h-16 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400 dark:text-slate-400">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif

                            <div class="flex-1 space-y-1">
                                <input type="file" wire:model="image" accept="image/*" class="block w-full text-xs text-gray-500 dark:text-slate-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-900/60 file:text-indigo-700 dark:file:text-indigo-200 hover:file:bg-indigo-100">
                                @if($image || $existingImageUrl)
                                    <button type="button" wire:click="removeImage" class="text-xs text-rose-600 dark:text-rose-400 hover:underline">Remove Image</button>
                                @endif
                                <span class="block text-[11px] text-gray-500 dark:text-slate-400">PNG, JPG, WEBP up to 2MB. Stored as BLOB in database.</span>
                            </div>
                        </div>

                        <div wire:loading wire:target="image" class="text-xs text-indigo-600 dark:text-indigo-400 mt-1 font-semibold">
                            Uploading image...
                        </div>
                        @error('image') <span class="text-xs text-rose-500 dark:text-rose-400 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-slate-200 uppercase mb-1">Product Name</label>
                        <input type="text" wire:model="name" class="w-full px-3 py-2 border rounded-lg text-sm text-gray-900 dark:text-slate-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-slate-400">
                        @error('name') <span class="text-xs text-rose-500 dark:text-rose-400">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-slate-200 uppercase mb-1">SKU</label>
                            <input type="text" wire:model="sku" placeholder="PROD-001" class="w-full px-3 py-2 border rounded-lg text-sm text-gray-900 dark:text-slate-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-slate-400">
                            @error('sku') <span class="text-xs text-rose-500 dark:text-rose-400">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-slate-200 uppercase mb-1">Category</label>
                            <select wire:model="category_id" class="w-full px-3 py-2 border rounded-lg text-sm text-gray-900 dark:text-slate-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600">
                                <option value="" class="dark:bg-gray-800 dark:text-slate-100">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" class="dark:bg-gray-800 dark:text-slate-100">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="text-xs text-rose-500 dark:text-rose-400">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-slate-200 uppercase mb-1">Selling Price (LKR)</label>
                            <input type="number" step="0.01" wire:model="selling_price" class="w-full px-3 py-2 border rounded-lg text-sm text-gray-900 dark:text-slate-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600">
                            @error('selling_price') <span class="text-xs text-rose-500 dark:text-rose-400">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-slate-200 uppercase mb-1">Low Stock Threshold</label>
                            <input type="number" wire:model="low_stock_threshold" class="w-full px-3 py-2 border rounded-lg text-sm text-gray-900 dark:text-slate-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600">
                            @error('low_stock_threshold') <span class="text-xs text-rose-500 dark:text-rose-400">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    @if(!$editingProductId)
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-slate-200 uppercase mb-1">Initial Quantity</label>
                            <input type="number" wire:model="initial_quantity" class="w-full px-3 py-2 border rounded-lg text-sm text-gray-900 dark:text-slate-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600">
                            @error('initial_quantity') <span class="text-xs text-rose-500 dark:text-rose-400">{{ $message }}</span> @enderror
                        </div>
                    @else
                        <div class="p-3 bg-amber-50 dark:bg-amber-900/40 rounded-lg text-xs text-amber-900 dark:text-amber-200 border border-amber-200 dark:border-amber-700">
                            <strong>Note:</strong> Product stock quantity cannot be edited directly from this screen. Use <strong>Stock In</strong> or <strong>Stock Out</strong> to maintain strict audit history.
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-slate-200 uppercase mb-1">Description</label>
                        <textarea wire:model="description" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm text-gray-900 dark:text-slate-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-slate-400"></textarea>
                        @error('description') <span class="text-xs text-rose-500 dark:text-rose-400">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 text-sm text-gray-600 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white font-medium">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 w-full max-w-md p-6 space-y-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-slate-100">Confirm Deletion</h3>
                <p class="text-sm text-gray-600 dark:text-slate-300">Are you sure you want to delete this product? Historical stock movements will be preserved.</p>
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button wire:click="$set('showDeleteModal', false)" class="px-4 py-2 text-sm text-gray-600 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white font-medium">Cancel</button>
                    <button wire:click="deleteProduct" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-medium rounded-lg shadow">Delete</button>
                </div>
            </div>
        </div>
    @endif
</div>
