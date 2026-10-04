<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StockMovementResource;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class StockMovementApiController extends Controller
{
    public function __construct(protected StockService $stockService)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = StockMovement::with(['product.category', 'user'])->latest();

        if ($request->filled('product_id')) {
            $query->forProduct($request->product_id);
        }

        if ($request->filled('type')) {
            $query->ofType($request->type);
        }

        if ($request->filled('reason')) {
            $query->ofReason($request->reason);
        }

        $movements = $query->paginate($request->integer('per_page', 15));

        return StockMovementResource::collection($movements);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => ['required', Rule::in(['in', 'out'])],
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:100',
            'note' => 'nullable|string|max:1000',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if ($validated['type'] === 'in') {
            $movement = $this->stockService->stockIn(
                $product,
                $request->user(),
                (int) $validated['quantity'],
                $validated['reason'],
                $validated['note'] ?? null
            );
        } else {
            $movement = $this->stockService->stockOut(
                $product,
                $request->user(),
                (int) $validated['quantity'],
                $validated['reason'],
                $validated['note'] ?? null
            );
        }

        $movement->load(['product.category', 'user']);

        return response()->json([
            'message' => 'Stock movement recorded successfully.',
            'data' => new StockMovementResource($movement),
        ], 201);
    }
}
