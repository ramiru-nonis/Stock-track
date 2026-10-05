<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use App\Services\CloudinaryService;
use App\Services\ExchangeRateService;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ProductsList extends Component
{
    use WithPagination;
    use WithFileUploads;
    use AuthorizesRequests;

    public string $search = '';
    public string $categoryFilter = '';
    
    // Form fields for create / edit modal
    public bool $showModal = false;
    public ?int $editingProductId = null;
    public string $name = '';
    public string $sku = '';
    public string $category_id = '';
    public string $description = '';
    public string $selling_price = '';
    public string $initial_quantity = '0';
    public string $low_stock_threshold = '5';
    /** @var \Illuminate\Http\UploadedFile|mixed */
    public $image = null; // TemporaryUploadedFile
    public ?string $existingImageUrl = null;

    // Delete confirmation
    public bool $showDeleteModal = false;
    public ?int $deletingProductId = null;

    protected function rules(): array
    {
        $skuRule = 'required|string|max:50|unique:products,sku';
        if ($this->editingProductId) {
            $skuRule .= ',' . $this->editingProductId;
        }

        $rules = [
            'name' => 'required|string|max:255',
            'sku' => $skuRule,
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:1000',
            'selling_price' => 'required|numeric|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048', // Max 2MB
        ];

        // Initial quantity is only set during creation
        if (!$this->editingProductId) {
            $rules['initial_quantity'] = 'required|integer|min:0';
        }

        return $rules;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->authorize('create', Product::class);

        $this->resetValidation();
        $this->reset(['editingProductId', 'name', 'sku', 'category_id', 'description', 'selling_price', 'initial_quantity', 'low_stock_threshold', 'image', 'existingImageUrl']);
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $product = Product::findOrFail($id);
        $this->authorize('update', $product);

        $this->resetValidation();
        $this->editingProductId = $product->id;
        $this->name = $product->name;
        $this->sku = $product->sku;
        $this->category_id = (string) $product->category_id;
        $this->description = (string) $product->description;
        $this->selling_price = (string) $product->selling_price;
        $this->low_stock_threshold = (string) $product->low_stock_threshold;
        $this->image = null;
        $this->existingImageUrl = $product->image_url;
        $this->showModal = true;
    }

    public function removeImage(CloudinaryService $cloudinaryService): void
    {
        if ($this->editingProductId) {
            $product = Product::findOrFail($this->editingProductId);
            $this->authorize('update', $product);

            if ($product->cloudinary_public_id) {
                $cloudinaryService->destroy($product->cloudinary_public_id);
            }

            $product->update([
                'image_data' => null,
                'image_mime' => null,
                'cloudinary_url' => null,
                'cloudinary_public_id' => null,
            ]);

            $this->existingImageUrl = null;
            session()->flash('message', 'Product image removed.');
        }

        $this->image = null;
    }

    public function saveProduct(CloudinaryService $cloudinaryService): void
    {
        if ($this->editingProductId) {
            $product = Product::findOrFail($this->editingProductId);
            $this->authorize('update', $product);
        } else {
            $this->authorize('create', Product::class);
        }

        $validated = $this->validate();

        $imageData = null;
        $imageMime = null;
        $cloudinaryUrl = null;
        $cloudinaryPublicId = null;

        if ($this->image) {
            // Save to XAMPP MySQL database BLOB storage
            $imageData = base64_encode(file_get_contents($this->image->getRealPath()));
            $imageMime = $this->image->getMimeType();

            // Save to Cloudinary CDN
            $cloudResult = $cloudinaryService->upload($this->image, 'products');
            if ($cloudResult) {
                $cloudinaryUrl = $cloudResult['secure_url'];
                $cloudinaryPublicId = $cloudResult['public_id'];
            }
        }

        if ($this->editingProductId) {
            $product = Product::findOrFail($this->editingProductId);

            // Destroy old Cloudinary asset if replacing
            if ($this->image && $product->cloudinary_public_id) {
                $cloudinaryService->destroy($product->cloudinary_public_id);
            }
            
            $updateData = [
                'name' => $validated['name'],
                'sku' => strtoupper($validated['sku']),
                'category_id' => $validated['category_id'],
                'description' => $validated['description'] ?? null,
                'selling_price' => $validated['selling_price'],
                'low_stock_threshold' => $validated['low_stock_threshold'],
            ];

            if ($this->image) {
                $updateData['image_data'] = $imageData;
                $updateData['image_mime'] = $imageMime;
                $updateData['cloudinary_url'] = $cloudinaryUrl;
                $updateData['cloudinary_public_id'] = $cloudinaryPublicId;
            }

            $product->update($updateData);
            session()->flash('message', 'Product updated successfully.');
        } else {
            Product::create([
                'name' => $validated['name'],
                'sku' => strtoupper($validated['sku']),
                'category_id' => $validated['category_id'],
                'description' => $validated['description'] ?? null,
                'selling_price' => $validated['selling_price'],
                'quantity' => $validated['initial_quantity'] ?? 0,
                'low_stock_threshold' => $validated['low_stock_threshold'],
                'image_data' => $imageData,
                'image_mime' => $imageMime,
                'cloudinary_url' => $cloudinaryUrl,
                'cloudinary_public_id' => $cloudinaryPublicId,
            ]);
            session()->flash('message', 'Product created successfully.');
        }

        $this->showModal = false;
        $this->reset(['image']);
    }

    public function confirmDelete(int $id): void
    {
        $product = Product::findOrFail($id);
        $this->authorize('delete', $product);

        $this->deletingProductId = $product->id;
        $this->showDeleteModal = true;
    }

    public function deleteProduct(CloudinaryService $cloudinaryService): void
    {
        if ($this->deletingProductId) {
            $product = Product::findOrFail($this->deletingProductId);
            $this->authorize('delete', $product);

            if ($product->cloudinary_public_id) {
                $cloudinaryService->destroy($product->cloudinary_public_id);
            }

            $product->delete();
            session()->flash('message', 'Product deleted successfully.');
        }

        $this->showDeleteModal = false;
        $this->deletingProductId = null;
    }

    #[Layout('layouts.app')]
    public function render(ExchangeRateService $exchangeRateService): View
    {
        $query = Product::with('category')->latest();

        if (!empty($this->search)) {
            $query->search($this->search);
        }

        if (!empty($this->categoryFilter)) {
            $query->where('category_id', $this->categoryFilter);
        }

        $products = $query->paginate(10);
        $categories = Category::orderBy('name')->get();
        $usdRate = $exchangeRateService->getRate('LKR', 'USD');

        return view('livewire.products-list', [
            'products' => $products,
            'categories' => $categories,
            'usdRate' => $usdRate,
        ]);
    }
}
