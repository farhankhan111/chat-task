<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\MessageRequest;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent;
class SmsController extends Controller
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
}
