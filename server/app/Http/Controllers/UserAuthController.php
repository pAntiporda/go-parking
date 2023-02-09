<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserAuthController extends Controller
{
    public function store(LoginUserRequest $request)
    {
        $user = User::where('email', $request->email)->firstOrFail();

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        $token = $user->createToken('web_token')->plainTextToken;

        return with(new UserResource($user))->additional([
            'data' => [
                'token' => $token,
            ],
        ]);
    }
}
