<div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b border-[#E2E8F0] dark:border-slate-700/60 pb-5">
        <div>
            <h1 class="text-2xl font-bold text-[#1F2937] dark:text-[#F8FAFC]">Shop Inventory Dashboard</h1>
            <p class="text-sm text-[#64748B] dark:text-[#CBD5E1] mt-1 font-medium">Real-time overview of products, stock alerts, and recent movements.</p>
        </div>
        <div class="mt-4 md:mt-0 flex items-center space-x-3">
            <a href="{{ route('stock.in') }}" class="inline-flex items-center px-4 py-2 bg-[#10B981] hover:bg-emerald-600 text-white text-sm font-medium rounded-lg shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Stock In
            </a>
            <a href="{{ route('stock.out') }}" class="inline-flex items-center px-4 py-2 bg-[#EF4444] hover:bg-rose-600 text-white text-sm font-medium rounded-lg shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                </svg>
                Stock Out
            </a>
        </div>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Total Products Card -->
        <div class="bg-white dark:bg-[#1F2A3A] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-slate-700/60 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-[#64748B] dark:text-[#CBD5E1]">Total Products</p>
                <p class="text-2xl font-extrabold text-[#1F2937] dark:text-[#F8FAFC] mt-1">{{ number_format($stats['total_products']) }}</p>
            </div>
            <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-950/60 text-[#6366F1] dark:text-indigo-400 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
        </div>

        <!-- Low Stock Card -->
        <div class="bg-white dark:bg-[#1F2A3A] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-slate-700/60 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-[#F59E0B] dark:text-amber-400">Low Stock Items</p>
                <p class="text-2xl font-extrabold text-[#F59E0B] dark:text-amber-400 mt-1">{{ number_format($stats['low_stock_count']) }}</p>
            </div>
            <div class="w-12 h-12 bg-amber-50 dark:bg-amber-950/60 text-[#F59E0B] dark:text-amber-400 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>

        <!-- Categories Card -->
        <div class="bg-white dark:bg-[#1F2A3A] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-slate-700/60 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-[#64748B] dark:text-[#CBD5E1]">Categories</p>
                <p class="text-2xl font-extrabold text-[#1F2937] dark:text-[#F8FAFC] mt-1">{{ number_format($stats['total_categories']) }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
            </div>
        </div>

        <!-- Recent Movements Card -->
        <div class="bg-white dark:bg-[#1F2A3A] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-slate-700/60 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-[#64748B] dark:text-[#CBD5E1]">Movements (7 days)</p>
                <p class="text-2xl font-extrabold text-[#1F2937] dark:text-[#F8FAFC] mt-1">{{ number_format($stats['recent_movements_count']) }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Recent Stock Movements Table -->
    <div class="bg-white dark:bg-[#1F2A3A] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-slate-700/60 overflow-hidden">
        <div class="p-5 border-b border-[#E2E8F0] dark:border-slate-700/60 flex justify-between items-center">
            <h2 class="text-lg font-bold text-[#1F2937] dark:text-[#F8FAFC]">Recent Stock Movements</h2>
            <a href="{{ route('low-stock') }}" class="text-xs text-[#6366F1] dark:text-indigo-400 hover:underline font-semibold">View Low Stock Alerts &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-[#F8FAFC] dark:bg-slate-900/60 text-xs font-bold text-[#64748B] dark:text-[#CBD5E1] uppercase tracking-wider border-b border-[#E2E8F0] dark:border-slate-700/60">
                    <tr>
                        <th class="px-6 py-3.5">Product</th>
                        <th class="px-6 py-3.5">Type</th>
                        <th class="px-6 py-3.5">Reason</th>
                        <th class="px-6 py-3.5">Quantity</th>
                        <th class="px-6 py-3.5">Performed By</th>
                        <th class="px-6 py-3.5">Date & Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-slate-700/60">
                    @forelse($recentMovements as $movement)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition">
                        <td class="px-6 py-4 font-semibold text-[#1F2937] dark:text-[#F8FAFC]">
                            <div class="flex items-center space-x-3">
                                @if($movement->product?->image_url)
                                    <img src="{{ $movement->product->image_url }}" alt="{{ $movement->product->name }}" class="w-10 h-10 rounded-lg object-cover border border-[#E2E8F0] dark:border-slate-700 shrink-0">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                                <div>
                                    <span class="block font-semibold text-[#1F2937] dark:text-[#F8FAFC]">{{ $movement->product?->name ?? 'Deleted Product' }}</span>
                                    <span class="block text-xs text-[#64748B] dark:text-[#CBD5E1] font-mono mt-0.5">SKU: {{ $movement->product?->sku ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($movement->type === 'in')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300">
                                &uarr; STOCK IN
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-950/80 dark:text-rose-300">
                                &darr; STOCK OUT
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 capitalize font-medium text-[#1F2937] dark:text-[#F8FAFC]">{{ $movement->reason }}</td>
                        <td class="px-6 py-4 font-mono font-bold text-[#1F2937] dark:text-[#F8FAFC]">{{ $movement->quantity }}</td>
                        <td class="px-6 py-4 text-xs font-medium text-[#64748B] dark:text-[#CBD5E1]">
                            {{ $movement->user?->name ?? 'System/Deactivated' }}
                        </td>
                        <td class="px-6 py-4 text-xs font-medium text-[#64748B] dark:text-[#CBD5E1]">
                            {{ $movement->created_at->format('M d, Y H:i') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-[#64748B] dark:text-[#CBD5E1]">
                            No recent stock movements recorded yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>