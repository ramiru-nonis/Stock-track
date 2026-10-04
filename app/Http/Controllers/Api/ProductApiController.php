<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\CloudinaryService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductApiController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Product::with('category')->latest();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->paginate($request->integer('per_page', 15));

        return ProductResource::collection($products);
    }

    public function show(Product $product): ProductResource
    {
        $product->load('category');

        return new ProductResource($product);
    }

    public function lowStock(Request $request): AnonymousResourceCollection
    {
        $products = Product::with('category')
            ->lowStock()
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return ProductResource::collection($products);
    }

    public function store(Request $request, CloudinaryService $cloudinaryService): JsonResponse
    {
        $this->authorize('create', Product::class);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:products,sku',
            'description' => 'nullable|string|max:1000',
            'selling_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $imageData = null;
        $imageMime = null;
        $cloudinaryUrl = null;
        $cloudinaryPublicId = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageData = base64_encode(file_get_contents($file->getRealPath()));
            $imageMime = $file->getMimeType();

            $cloudResult = $cloudinaryService->upload($file, 'products');
            if ($cloudResult) {
                $cloudinaryUrl = $cloudResult['secure_url'];
                $cloudinaryPublicId = $cloudResult['public_id'];
            }
        }

        $product = Product::create([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'sku' => strtoupper($validated['sku']),
            'description' => $validated['description'] ?? null,
            'selling_price' => $validated['selling_price'],
            'quantity' => $validated['quantity'],
            'low_stock_threshold' => $validated['low_stock_threshold'],
            'image_data' => $imageData,
            'image_mime' => $imageMime,
            'cloudinary_url' => $cloudinaryUrl,
            'cloudinary_public_id' => $cloudinaryPublicId,
        ]);

        $product->load('category');

        return response()->json([
            'message' => 'Product created successfully via API.',
            'data' => new ProductResource($product),
        ], 201);
    }

    public function update(Request $request, Product $product, CloudinaryService $cloudinaryService): JsonResponse
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:products,sku,' . $product->id,
            'description' => 'nullable|string|max:1000',
            'selling_price' => 'required|numeric|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $updateData = [
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'sku' => strtoupper($validated['sku']),
            'description' => $validated['description'] ?? null,
            'selling_price' => $validated['selling_price'],
            'low_stock_threshold' => $validated['low_stock_threshold'],
        ];

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            if ($product->cloudinary_public_id) {
                $cloudinaryService->destroy($product->cloudinary_public_id);
            }

            $updateData['image_data'] = base64_encode(file_get_contents($file->getRealPath()));
            $updateData['image_mime'] = $file->getMimeType();

            $cloudResult = $cloudinaryService->upload($file, 'products');
            if ($cloudResult) {
                $updateData['cloudinary_url'] = $cloudResult['secure_url'];
                $updateData['cloudinary_public_id'] = $cloudResult['public_id'];
            }
        }

        $product->update($updateData);
        $product->load('category');

        return response()->json([
            'message' => 'Product updated successfully via API.',
            'data' => new ProductResource($product),
        ]);
    }

    public function destroy(Product $product, CloudinaryService $cloudinaryService): JsonResponse
    {
        $this->authorize('delete', $product);

        if ($product->cloudinary_public_id) {
            $cloudinaryService->destroy($product->cloudinary_public_id);
        }

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully via API.',
        ]);
    }
}
