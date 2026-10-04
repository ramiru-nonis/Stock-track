<div class="py-6 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="border-b border-gray-200 dark:border-gray-700 pb-5">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
            <span class="w-8 h-8 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center mr-2">&darr;</span>
            Record Stock Out
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Record sales, damaged/expired inventory write-offs, or negative stock adjustments.</p>
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

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8">
        <form wire:submit.prevent="recordStockOut" class="space-y-6">
            <!-- Select Product -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-2">Select Product *</label>
                <select wire:model.live="product_id" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-sm dark:bg-gray-700 dark:text-white focus:ring-rose-500 focus:border-rose-500">
                    <option value="">-- Choose Product --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">
                            {{ $product->name }} (SKU: {{ $product->sku }}) - Current Stock: {{ $product->quantity }}
                        </option>
                    @endforeach
                </select>
                @error('product_id') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Current Stock Display Box -->
            @if($selectedProduct)
                <div class="p-4 bg-rose-50/50 dark:bg-rose-900/20 rounded-xl border border-rose-100 dark:border-rose-800 flex items-center justify-between">
                    <div>
                        <p class="text-xs text-rose-800 dark:text-rose-300 font-semibold uppercase">Current Stock Available</p>
                        <p class="text-lg font-extrabold text-rose-900 dark:text-rose-200 mt-0.5">{{ $selectedProduct->name }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-3xl font-black text-rose-700 dark:text-rose-400">{{ $selectedProduct->quantity }}</span>
                        <span class="text-xs text-gray-500 block">units available</span>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Quantity -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-2">Quantity to Remove *</label>
                    <input type="number" min="1" max="{{ $selectedProduct?->quantity ?? 99999 }}" wire:model="quantity" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-sm dark:bg-gray-700 dark:text-white focus:ring-rose-500 focus:border-rose-500 font-mono font-bold">
                    @error('quantity') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Reason -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-2">Reason for Stock Out *</label>
                    <select wire:model="reason" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-sm dark:bg-gray-700 dark:text-white focus:ring-rose-500 focus:border-rose-500">
                        <option value="sale">Customer Sale</option>
                        <option value="damage">Damage / Expired / Loss</option>
                        <option value="adjustment">Stock Adjustment (Negative)</option>
                    </select>
                    @error('reason') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Optional Note -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-2">Optional Audit Note</label>
                <textarea wire:model="note" rows="3" placeholder="Add receipt reference, customer note, or damage description..." class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-sm dark:bg-gray-700 dark:text-white focus:ring-rose-500 focus:border-rose-500"></textarea>
                @error('note') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                <button type="submit" class="px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm rounded-xl shadow-md transition flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                    Confirm Stock Out Transaction
                </button>
            </div>
        </form>
    </div>
</div>
