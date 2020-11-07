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
        <conversations :paginated-conversations="{{ $conversations }}"></conversations>
    </main>


</x-layouts._forum>