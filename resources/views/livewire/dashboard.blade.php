<div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b border-gray-200 dark:border-gray-700 pb-5">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Shop Inventory Dashboard</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Real-time overview of products, stock alerts, and recent movements.</p>
        </div>
        <div class="mt-4 md:mt-0 flex items-center space-x-3">
            <a href="{{ route('stock.in') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg shadow transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Stock In
            </a>
            <a href="{{ route('stock.out') }}" class="inline-flex items-center px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-medium rounded-lg shadow transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                Stock Out
            </a>
        </div>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Total Products Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Products</p>
                <p class="text-2xl font-extrabold text-gray-900 dark:text-white mt-1">{{ number_format($stats['total_products']) }}</p>
            </div>
            <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
        </div>

        <!-- Low Stock Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-400">Low Stock Items</p>
                <p class="text-2xl font-extrabold text-amber-600 dark:text-amber-400 mt-1">{{ number_format($stats['low_stock_count']) }}</p>
            </div>
            <div class="w-12 h-12 bg-amber-50 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
        </div>

        <!-- Categories Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Categories</p>
                <p class="text-2xl font-extrabold text-gray-900 dark:text-white mt-1">{{ number_format($stats['total_categories']) }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            </div>
        </div>

        <!-- Recent Movements Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Movements (7 days)</p>
                <p class="text-2xl font-extrabold text-gray-900 dark:text-white mt-1">{{ number_format($stats['recent_movements_count']) }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-50 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            </div>
        </div>
    </div>

    <!-- Recent Stock Movements Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Recent Stock Movements</h2>
            <a href="{{ route('low-stock') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-semibold">View Low Stock Alerts &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3">Product</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Reason</th>
                        <th class="px-6 py-3">Quantity</th>
                        <th class="px-6 py-3">Performed By</th>
                        <th class="px-6 py-3">Date & Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($recentMovements as $movement)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                            <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
                                {{ $movement->product?->name ?? 'Deleted Product' }}
                                <span class="block text-xs text-gray-400">SKU: {{ $movement->product?->sku ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($movement->type === 'in')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">
                                        &uarr; STOCK IN
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-900/50 dark:text-rose-300">
                                        &darr; STOCK OUT
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 capitalize font-medium">{{ $movement->reason }}</td>
                            <td class="px-6 py-4 font-mono font-bold">{{ $movement->quantity }}</td>
                            <td class="px-6 py-4 text-xs text-gray-500 dark:text-gray-400">
                                {{ $movement->user?->name ?? 'System/Deactivated' }}
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500 dark:text-gray-400">
                                {{ $movement->created_at->format('M d, Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                No recent stock movements recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
