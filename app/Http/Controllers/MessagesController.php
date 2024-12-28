<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\MessageRequest;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent;
class MessagesController extends Controller
{
    public function store(MessageRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['sender_id'] = Auth::guard('api')->user()->id;

        $message = Message::create($data);

        broadcast(new MessageSent($message));

        return response()->json([
            'success'=> true,
            'message' => 'Message sent successfully.',
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
