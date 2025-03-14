<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|min:6|confirmed',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'Invalid credentials',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
        $user = $this->userRepository->create($validatedData);
        $token = $user->createToken($user->email);
        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'token' => $token->plainTextToken,
            'created_at' => $user->created_at,
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users',
                'password' => 'required|min:6',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'Invalid credentials',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $user = $this->userRepository->findByEmail($request->input('email'));
        if (
            !$user
            || !Hash::check(
                $request->input('password'),
                $user->getAuthPassword())
        ) {
            return response()->json(
                [
                    'error' => 'Invalid credentials'
                ],
                Response::HTTP_BAD_REQUEST,
            );
        }

        $token = $user->createToken($user->email);
        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'token' => $token->plainTextToken,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
