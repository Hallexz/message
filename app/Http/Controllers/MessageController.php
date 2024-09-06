<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    public function send(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id',
            'content'=> 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $message = Message::create([
            'sender_id' => $request->sender_id,
            'receiver_id' => $request->receiver_id,
            'content' => $request->content,
        ]);

        return response()->json($message, 201);
    }

    public function receive(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:user,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $messages = Message::where('receiver_id', $request->user_id)
            ->orWhere('sender_id', $request->user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($messages);
    }

    public function delete($id)
    {
        $message = Message::find($id);

        if (!$message) {
            return response()->json(['message' => 'message not found'], 404);
        } 

        $message->delete();

        return response()->json(['message' => 'message deleted successfully']);
    }

    public function markAsRead($id)
    {
        $message = Message::find($id);

        if (!$message) {
            return response()->json(['message' => 'message not found'], 404);
        }

        $message->read = true;
        $message->save();

        return response()-json(['message' => 'message marked as read']);
    }
}
