<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        // 認証成功時はtokenを返す
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('Laravel Password Grant Client')->accessToken;
            return new JsonResponse(['token' => $token], 200);
        }

        // 認証失敗時はエラーメッセージを返す
        return new JsonResponse([
            'message' => 'Unauthenticated.'
        ], 401);
    }
}
