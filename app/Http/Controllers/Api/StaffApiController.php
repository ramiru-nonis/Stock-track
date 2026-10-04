<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Hash;

class StaffApiController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', User::class);

        $staff = User::where('role', 'staff')
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return UserResource::collection($staff);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $staff = User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'role' => 'staff',
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Staff account created successfully via API.',
            'data' => new UserResource($staff),
        ], 201);
    }

    public function destroy(User $user): JsonResponse
    {
        $this->authorize('delete', $user);

        if ($user->isOwner()) {
            return response()->json([
                'message' => 'Cannot deactivate owner accounts.',
            ], 422);
        }

        $user->is_active = false;
        $user->save();
        $user->delete();

        return response()->json([
            'message' => 'Staff account deactivated successfully via API. Historical audit records preserved.',
        ]);
    }
}
