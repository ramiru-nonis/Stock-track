<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function issueToken(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'nullable|string',
        ]);

        /** @var User|null $user */
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password) || !$user->is_active) {
            return response()->json([
                'message' => 'The provided credentials are invalid or account is inactive.',
            ], 401);
        }

        $abilities = $user->isOwner() ? ['*'] : ['stock:read', 'stock:write'];
        $token = $user->createToken($request->device_name ?? 'api-token', $abilities)->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    public function revokeToken(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Token revoked successfully.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($request->user()),
        ]);
    }
}
