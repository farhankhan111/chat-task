<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::where('id', '!=', Auth::guard('api')->user()->id)->get();
        return response()->json(['success' => true, 'users' => $users]);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->accessToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }


    public function getUserMessages(Request $request): JsonResponse
    {
        $authId = Auth::guard('api')->user()->id;

        $messages = Message::where(function($query) use ($authId, $request) {
            $query->where('sender_id', $authId)
                ->where('receiver_id', $request->receiver_id)
                ->orWhere('sender_id', $request->receiver_id)
                ->where('receiver_id', $authId);
        })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->sortBy('created_at')
            ->values()
            ->toArray();


        return response()->json(['success' => true, 'messages' => $messages]);
    }

}
