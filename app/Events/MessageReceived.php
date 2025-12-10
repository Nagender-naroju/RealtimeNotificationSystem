<?php
// app/Events/MessageReceived.php
namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class MessageReceived implements ShouldBroadcast
{
    use SerializesModels;

    public $messagePayload;

    public function __construct(Message $message)
    {
        // Provide only the data you want broadcasted
        $this->messagePayload = [
            'id' => $message->id,
            'sender_id' => $message->sender_id,
            'body' => $message->body,
            'meta' => $message->meta,
            'created_at' => $message->created_at->toDateTimeString(),
        ];
    }

    // channel name
    public function broadcastOn()
    {
        return new Channel('messages.channel');
    }

    // event name under which clients will listen
    public function broadcastAs()
    {
        return 'message.received';
    }

    // payload Laravel will use is this public property ($messagePayload)
}
