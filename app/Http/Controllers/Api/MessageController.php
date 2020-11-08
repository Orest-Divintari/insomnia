<?php

namespace App\Http\Controllers\Api;

use App\Conversation;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateMessageRequest;
use App\Reply;

class MessageController extends Controller
{
    /**
     * Get the messages for the specified conversation
     *
     * @param Reply $message
     * @return \Illuminate\Http\Response
     */
    public function show(Reply $message)
    {
        $this->authorize('view', $message->repliable);

        return redirect(
            route('conversations.show', $message->repliable) .
            "?page=" . $message->pageNumber .
            '#convMessage-' . $message->id
        );
    }

    /**
     * Store a new message in the database
     *
     * @param Conversation $conversation
     * @return \Illuminate\Http\Response
     */
    public function store(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $message = request()->validate([
            'body' => ['string', 'required'],
        ]);

        return $conversation
            ->addMessage($message['body'])
            ->load('poster');
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