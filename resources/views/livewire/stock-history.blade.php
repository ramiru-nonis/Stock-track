<div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="border-b border-gray-200 dark:border-gray-700 pb-5 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                <svg class="w-7 h-7 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Complete Stock Audit History (Owner Administration)
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Full immutable ledger of every inventory change, user action, and stock movement.</p>
        </div>
        <button wire:click="resetFilters" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-xs font-semibold text-gray-700 dark:text-gray-300 rounded-lg">
            Reset Filters
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
        <!-- Product Filter -->
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Product</label>
            <select wire:model.live="productId" class="w-full text-xs py-2 border rounded-lg dark:bg-gray-700 dark:text-white border-gray-300 dark:border-gray-600">
                <option value="">All Products</option>
                @foreach($products as $prod)
                    <option value="{{ $prod->id }}">{{ $prod->name }} ({{ $prod->sku }})</option>
                @endforeach
            </select>
        </div>

        <!-- User Filter -->
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">User</label>
            <select wire:model.live="userId" class="w-full text-xs py-2 border rounded-lg dark:bg-gray-700 dark:text-white border-gray-300 dark:border-gray-600">
                <option value="">All Users</option>
                @foreach($users as $usr)
                    <option value="{{ $usr->id }}">{{ $usr->name }} ({{ ucfirst($usr->role) }})</option>
                @endforeach
            </select>
        </div>

        <!-- Type Filter -->
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Type</label>
            <select wire:model.live="type" class="w-full text-xs py-2 border rounded-lg dark:bg-gray-700 dark:text-white border-gray-300 dark:border-gray-600">
                <option value="">All Types</option>
                <option value="in">Stock In</option>
                <option value="out">Stock Out</option>
            </select>
        </div>

        <!-- Reason Filter -->
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Reason</label>
            <select wire:model.live="reason" class="w-full text-xs py-2 border rounded-lg dark:bg-gray-700 dark:text-white border-gray-300 dark:border-gray-600">
                <option value="">All Reasons</option>
                <option value="restock">Restock</option>
                <option value="sale">Sale</option>
                <option value="damage">Damage</option>
                <option value="adjustment">Adjustment</option>
            </select>
        </div>

        <!-- Date From -->
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">From Date</label>
            <input type="date" wire:model.live="fromDate" class="w-full text-xs py-2 border rounded-lg dark:bg-gray-700 dark:text-white border-gray-300 dark:border-gray-600">
        </div>

        <!-- Date To -->
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">To Date</label>
            <input type="date" wire:model.live="toDate" class="w-full text-xs py-2 border rounded-lg dark:bg-gray-700 dark:text-white border-gray-300 dark:border-gray-600">
        </div>
    </div>

    <!-- Audit Ledger Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
            <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs font-bold text-gray-500 uppercase">
                <tr>
                    <th class="px-6 py-3">Timestamp</th>
                    <th class="px-6 py-3">Product</th>
                    <th class="px-6 py-3">SKU</th>
                    <th class="px-6 py-3">Type</th>
                    <th class="px-6 py-3">Reason</th>
                    <th class="px-6 py-3">Quantity</th>
                    <th class="px-6 py-3">User</th>
                    <th class="px-6 py-3">Note</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($movements as $mv)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                        <td class="px-6 py-4 font-mono text-xs text-gray-500">{{ $mv->created_at->format('Y-m-d H:i:s') }}</td>
                        <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                            <div class="flex items-center space-x-3">
                                @if($mv->product?->image_url)
                                    <img src="{{ $mv->product->image_url }}" alt="{{ $mv->product->name }}" class="w-9 h-9 rounded-lg object-cover border border-gray-200 dark:border-gray-700 shrink-0">
                                @else
                                    <div class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400 shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                                <span>{{ $mv->product?->name ?? 'Deleted Product' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-mono text-xs">{{ $mv->product?->sku ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            @if($mv->type === 'in')
                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">&uarr; IN</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-900/50 dark:text-rose-300">&darr; OUT</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 capitalize font-semibold">{{ $mv->reason }}</td>
                        <td class="px-6 py-4 font-mono font-bold">{{ $mv->quantity }}</td>
                        <td class="px-6 py-4 text-xs font-medium text-gray-900 dark:text-white">
                            {{ $mv->user?->name ?? 'System/Deactivated User' }}
                            @if($mv->user?->trashed())
                                <span class="text-xs text-rose-400 block">(Deactivated)</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-500 truncate max-w-xs">{{ $mv->note ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-400">No stock movements found for the selected filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
            {{ $movements->links() }}
        </div>
    </div>
</div>
