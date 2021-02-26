<?php

namespace App\Http\Controllers;

use App\Conversation;
use App\Events\Activity\UserViewedPage;
use App\Filters\FilterManager;
use App\Http\Requests\CreateConversationRequest;
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
            ->withIsStarred()
            ->firstOrFail();

        $messages = $conversation->messages()->withLikes()
            ->paginate(Conversation::REPLIES_PER_PAGE);

        $participants = $conversation->participants;

        if (request()->expectsJson()) {
            return compact('conversation', 'messages', 'participants');
        }

        return view(
            'conversations.show',
            compact('conversation', 'messages')
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
            ->filter($filters)
            ->withHasBeenUpdated()
            ->withIsStarred()
            ->withRecentMessage()
            ->with(['starter', 'participants'])
            ->withCount(['messages', 'participants'])
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