<x-layouts.forum>
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
        @if(empty($conversations->items()) && !$conversationFilters)
        <p class="mt-5 bg-white text-sm text-black-semi p-4 shadow-lg rounded">There are no conversations to display.
        </p>
        @else
        <conversations :paginated-conversations="{{ json_encode($conversations) }}"
            :conversation-filters="{{ json_encode($conversationFilters) }}">
        </conversations>
        @endif
    </main>


</x-layouts.forum>
