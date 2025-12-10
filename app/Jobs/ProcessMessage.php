<?php
// app/Jobs/ProcessMessage.php
namespace App\Jobs;

use App\Events\MessageReceived;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $messageId;

    public function __construct(int $messageId)
    {
        $this->messageId = $messageId;
    }

    public function handle()
    {
        // Fetch the message from DB
        $message = Message::find($this->messageId);
        if (! $message) {
            return;
        }

        // Example processing: sanitize and add metadata.
        // Use a sanitizer library or simple strip_tags/trim here.
        $cleanBody = trim(strip_tags($message->body));

        // Append metadata
        $meta = $message->meta ?? [];
        $meta['sanitized'] = true;
        $meta['sanitized_at'] = now()->toDateTimeString();
        $meta['length'] = mb_strlen($cleanBody);

        // Save processed result
        $message->body = $cleanBody;
        $message->meta = $meta;
        $message->processed = true;
        $message->save();

        // Broadcast event to all connected clients
        event(new MessageReceived($message));
    }
}
