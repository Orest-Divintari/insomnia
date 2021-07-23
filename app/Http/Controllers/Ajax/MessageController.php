<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use App\Models\Conversation;
use App\Models\Reply;

class MessageController extends Controller
{
    /**
     * Store a new message in the database
     *
     * @param Conversation $conversation
     * @return \Illuminate\Http\Response
     */
    public function store(Conversation $conversation, CreateMessageRequest $request)
    {
        return $conversation
            ->addMessage($request->validated())
            ->load('poster')
            ->append('permissions');
    }

    /**
     * Update an existing conversation message
     *
     * @param Reply $message
     * @param UpdateMessageRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(Reply $message, UpdateMessageRequest $request)
    {
        $request->update($message);

        return response('Message has been updated', 200);
    }
}
