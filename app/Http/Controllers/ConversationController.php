<?php

namespace App\Http\Controllers;

use App\Actions\AppendHasIgnoredContentAttributeAction;
use App\Conversation;
use App\Events\Activity\UserViewedPage;
use App\Filters\ExcludeIgnoredFilter;
use App\Filters\FilterManager;
use App\Http\Requests\CreateConversationRequest;
use App\ViewModels\ConversationsShowViewModel;
use Illuminate\Http\Request;

class ConversationController extends Controller
{

    /**
     * Store a new conversation and send message to participant
     *
     * @param CreateConversationRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateConversationRequest $conversationRequest)
    {
        $conversation = $conversationRequest->persist();

        return redirect(route('conversations.show', $conversation));
    }

    /**
     * Display the form for creating a new conversation
     *
     * @return \Illuminate\View\View
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
    public function show(Conversation $conversation, AppendHasIgnoredContentAttributeAction $hasIgnoredContentAction)
    {
        $this->authorize('view', $conversation);

        event(new UserViewedPage(UserViewedPage::CONVERSATION));

        $conversation->read();

        $viewModel = new ConversationsShowViewModel($conversation, $hasIgnoredContentAction);

        return view(
            'conversations.show',
            [
                'conversation' => $viewModel->conversation(),
                'messages' => $viewModel->messages(),
                'participants' => $viewModel->participants(),
            ]
        );
    }

    /**
     * Display the conversations of the authenticated user
     *
     * @return \Illuminate\View\View
     */
    public function index(FilterManager $filterManager, ExcludeIgnoredFilter $excludeIgnoredFilter)
    {
        event(new UserViewedPage(UserViewedPage::CONVERSATION));

        $conversationFilters = $filterManager->withConversationFilters();

        $conversations = auth()->user()
            ->conversations()
            ->excludeIgnored(auth()->user(), $excludeIgnoredFilter)
            ->filter($conversationFilters)
            ->withHasBeenUpdated()
            ->withIsStarred()
            ->withRecentMessage()
            ->with(['starter', 'participants'])
            ->withCount(['messages', 'participants'])
            ->latest('conversations.updated_at')
            ->paginate(Conversation::PER_PAGE);

        $conversationFilters = $conversationFilters->getRequestedFilters();

        return view(
            'conversations.index',
            compact('conversations', 'conversationFilters')
        );
    }
}