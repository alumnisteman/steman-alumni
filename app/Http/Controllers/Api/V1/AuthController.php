<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Http\Resources\AuthResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($this->authService->login($request->only('email', 'password'))) {
            $user = Auth::user();
            
            // To use Sanctum:
            // $token = $this->authService->generateApiToken($user);
            // $this is a fallback token generator if sanctum is missing
            $token = method_exists($user, 'createToken') ? $this->authService->generateApiToken($user) : 'fallback_api_token_here_if_sanctum_is_not_installed';

            return new AuthResource([
                'user' => $user,
                'token' => $token
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }
    
    public function register(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:4|confirmed',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $data = $request->only('name', 'email', 'password');
        $data['role'] = 'alumni';
        
        $user = $this->authService->register($data);
        $token = method_exists($user, 'createToken') ? $this->authService->generateApiToken($user) : 'fallback_api_token_missing_sanctum';
        
        return new AuthResource([
            'user' => $user,
            'token' => $token
        ]);
    }
}
