<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductApiController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Product::with('category')->latest();

        if ($request->filled('search')) {
            $query->search($request->search);
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
}
