<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Reply;

class MessageController extends Controller
{
    /**
     * Display the messages for the specified conversation
     *
     * @param Reply $message
     * @return \Illuminate\Http\Response
     */
    public function show($messageId)
    {
        $message = Reply::where('repliable_type', Conversation::class)
            ->whereId($messageId)
            ->firstOrFail();

        $this->authorize('view', $message->repliable);

        return redirect($message->path);
    }
}
