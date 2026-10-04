<div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="border-b border-gray-200 dark:border-gray-700 pb-5 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-amber-600 dark:text-amber-400 flex items-center">
                <svg class="w-7 h-7 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Low Stock Alert List
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Products currently at or below their assigned low-stock threshold.</p>
        </div>
        <a href="{{ route('stock.in') }}" class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg shadow">
            &uarr; Restock Now
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Filter low stock items by name or SKU..."
            class="w-full md:w-1/3 px-4 py-2 bg-gray-50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white">
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
            <thead class="bg-amber-50/50 dark:bg-amber-950/20 text-xs font-bold text-amber-800 dark:text-amber-300 uppercase">
                <tr>
                    <th class="px-6 py-3">Image</th>
                    <th class="px-6 py-3">SKU</th>
                    <th class="px-6 py-3">Product Name</th>
                    <th class="px-6 py-3">Category</th>
                    <th class="px-6 py-3">Current Stock</th>
                    <th class="px-6 py-3">Threshold</th>
                    <th class="px-6 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($products as $product)
                    <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-900/10">
                        <td class="px-6 py-4">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-10 h-10 rounded-lg object-cover border border-amber-200 dark:border-amber-700 shadow-sm">
                            @else
                                <div class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center text-amber-600 dark:text-amber-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-mono font-bold text-gray-900 dark:text-white">{{ $product->sku }}</td>
                        <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ $product->name }}</td>
                        <td class="px-6 py-4 text-xs">{{ $product->category?->name ?? 'Uncategorized' }}</td>
                        <td class="px-6 py-4 font-black text-amber-600 dark:text-amber-400 text-base">
                            {{ $product->quantity }}
                        </td>
                        <td class="px-6 py-4 text-xs font-semibold text-gray-500">{{ $product->low_stock_threshold }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('stock.in') }}" class="inline-flex items-center px-3 py-1 bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300 rounded-md text-xs font-bold hover:bg-emerald-200">
                                Restock Item
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                            No low-stock alerts right now! All product inventory levels are healthy.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
            {{ $products->links() }}
        </div>
    </div>
</div>
