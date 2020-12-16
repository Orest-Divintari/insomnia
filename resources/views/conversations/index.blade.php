<x-layouts._forum>
    <header class="w-full flex items-center justify-between">
        <h1 class="section-title">Conversations</h1>
        <div class="py-4">
            <a href="{{ route('conversations.create')  }}" class="btn-post">
                <span class="fas fa-pen text-white text-xs mr-1"></span>
                Start Conversation
            </a>
        </div>

    </header>

    <main class="section">
        @if(empty($conversations->items()))
        <p class="mt-5 bg-white text-sm text-black-semi p-4 shadow-lg rounded">You have no conversations yet.</p>
        @else
        <conversations :conversation-filters="{{ json_encode($conversationFilters) }}"
            :paginated-conversations="{{ json_encode($conversations) }}"></conversations>
        @endif
    </main>


</x-layouts._forum>