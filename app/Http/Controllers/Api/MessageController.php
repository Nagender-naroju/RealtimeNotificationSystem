<?php

// app/Http/Controllers/Api/MessageController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessMessage;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sender_id' => 'required|integer',
            'message' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // create message (unprocessed)
        $message = Message::create([
            'sender_id' => $request->input('sender_id'),
            'body' => $request->input('message'),
            'meta' => null,
            'processed' => false,
        ]);

        // dispatch job to process in background
        ProcessMessage::dispatch($message->id);

        return response()->json([
            'message_id' => $message->id,
            'status' => 'queued',
        ], 201);
    }
}
