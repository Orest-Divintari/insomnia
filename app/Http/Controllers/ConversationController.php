<?php

namespace App\Http\Controllers;

use App\Conversation;
use App\Http\Requests\CreateConversationRequest;
use App\Reply;
use Illuminate\Http\Request;

class ConversationController extends Controller
{

    /**
     * Store a new conversation and send message to participant
     *
     * @param CreateConversationRequest $request
     * @return void
     */
    public function store(Request $request, CreateConversationRequest $conversationRequest)
    {
        $conversation = $conversationRequest->persist();
        $conversation->addParticipants(
            $request->input('participants')
        );
        $conversation->addMessage(
            $request->input('message')
        );

        return redirect(route('conversations.show', $conversation));
    }

    /**
     * Show the form for creating a new conversation
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('conversations.create');
    }

    /**
     * Display a specific conversation
     *
     * @param Conversation $conversation
     * @return Illuminate\View\View
     */
    public function show(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $participants = $conversation->participants;

        $messages = Reply::forRepliable($conversation);

        if (request()->expectsJson()) {
            return compact('conversation', 'participants', 'messages');
        }

        return view(
            'conversations.show',
            compact('conversation', 'messages', 'participants')
        );
    }

    /**
     * Display the conversations of the authenticated user
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $conversations = auth()->user()
            ->conversations()
            ->withStarter()
            ->withRecentMessage()
            ->with('participants')
            ->withCount('participants')
            ->withCount('messages')
            ->latest()
            ->paginate(Conversation::PER_PAGE)
            ->toJson();

        if (request()->expectsJson()) {
            return $conversations;
        }

        return view('conversations.index', compact('conversations'));
    }
}