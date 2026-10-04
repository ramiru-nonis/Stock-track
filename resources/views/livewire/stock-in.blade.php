<div class="py-6 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="border-b border-gray-200 dark:border-gray-700 pb-5">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
            <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center mr-2">&uarr;</span>
            Record Stock In
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Receive inventory from suppliers, restock shelves, or record positive inventory adjustments.</p>
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
        <form wire:submit.prevent="recordStockIn" class="space-y-6">
            <!-- Select Product -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-2">Select Product *</label>
                <select wire:model.live="product_id" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-sm dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500">
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
                <div class="p-4 bg-emerald-50/50 dark:bg-emerald-900/20 rounded-xl border border-emerald-100 dark:border-emerald-800 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        @if($selectedProduct->image_url)
                            <img src="{{ $selectedProduct->image_url }}" alt="{{ $selectedProduct->name }}" class="w-14 h-14 rounded-xl object-cover border border-emerald-200 dark:border-emerald-700 shadow-sm shrink-0">
                        @else
                            <div class="w-14 h-14 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                        <div>
                            <p class="text-xs text-emerald-800 dark:text-emerald-300 font-semibold uppercase">Current Stock Before Entry</p>
                            <p class="text-lg font-extrabold text-emerald-900 dark:text-emerald-200 mt-0.5">{{ $selectedProduct->name }}</p>
                            <p class="text-xs text-emerald-700 dark:text-emerald-400 font-mono">SKU: {{ $selectedProduct->sku }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-3xl font-black text-emerald-700 dark:text-emerald-400">{{ $selectedProduct->quantity }}</span>
                        <span class="text-xs text-gray-500 block">units available</span>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Quantity -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-2">Quantity to Add *</label>
                    <input type="number" min="1" wire:model="quantity" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-sm dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500 font-mono font-bold">
                    @error('quantity') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Reason -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-2">Reason *</label>
                    <select wire:model="reason" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-sm dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="restock">Restock / Supplier Delivery</option>
                        <option value="adjustment">Stock Adjustment (Positive)</option>
                    </select>
                    @error('reason') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Optional Note -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-2">Optional Audit Note</label>
                <textarea wire:model="note" rows="3" placeholder="Add invoice number, supplier details, or reason note..." class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-sm dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                @error('note') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl shadow-md transition flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Confirm Stock In Transaction
                </button>
            </div>
        </form>
    </div>
</div>
