<?php

namespace App\Http\Controllers;

use App\Conversation;
use App\Events\Activity\UserViewedPage;
use App\Filters\FilterManager;
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
    public function store(CreateConversationRequest $conversationRequest)
    {
        $conversation = $conversationRequest->persist();

        return redirect(route('conversations.show', $conversation));
    }

    /**
     * Show the form for creating a new conversation
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view(
            'conversations.create',
            ['participant' => request('add_participant')]
        );
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

        event(new UserViewedPage(UserViewedPage::CONVERSATION));

        auth()->user()->read($conversation);

        $conversation = Conversation::whereSlug($conversation->slug)
            ->withHasBeenUpdated()
            ->with('participants')
            ->isStarred()
            ->firstOrFail();

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
    public function index(FilterManager $filterManager)
    {
        event(new UserViewedPage(UserViewedPage::CONVERSATION));

        $filters = $filterManager->withConversationFilters();

        $conversations = auth()->user()
            ->conversations()
            ->withHasBeenUpdated()
            ->isStarred()
            ->filter($filters)
            ->with('starter')
            ->withRecentMessage()
            ->with('participants')
            ->withCount('participants')
            ->withCount('messages')
            ->latest('conversations.created_at')
            ->paginate(Conversation::PER_PAGE);

        $conversationFilters = $filters->getRequestedFilters();

        if (request()->expectsJson()) {
            return $conversations;
        }

        return view(
            'conversations.index',
            compact('conversations', 'conversationFilters')
        );
    }
}