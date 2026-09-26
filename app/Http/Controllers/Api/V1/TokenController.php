<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class TokenController extends Controller
{
    /**
     * POST /api/v1/tokens with username, password and a name for the token.
     * Tokens can also be created from the settings page.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'name' => ['required', 'string', 'max:60'],
        ]);

        $user = User::whereUsername($data['username'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['username' => "That username and password don't match."]);
        }

        abort_if($user->isBanned(), 403, 'This account has been suspended.');

        return response()->json(['token' => $user->createToken($data['name'])->plainTextToken], 201);
    }

    /**
     * DELETE /api/v1/tokens/current revokes the token used for the request.
     */
    public function destroy(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}
