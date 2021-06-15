<x-layouts.ignored ignoredType="threads">
    <div class="border border-gray-ligghter rounded " v-cloak>
        @forelse($ignoredThreads as $ignoredThread)
        <div class="flex items-center pr-2 {{ $loop->last ? 'border-b-0' : 'border-b' }}">
            <thread-list-item class="flex-1" :thread="{{ $ignoredThread }}"></thread-list-item>
            <ignore-thread-button :thread="{{ $thread }}"
                :ignored="{{ json_encode($ignoredThread->ignored_by_visitor) }}">
        </div>

        @empty
        <p class="text-md p-7/2">You are not currently ignoring any threads.</p>
        @endforelse
</x-layouts.ignored>