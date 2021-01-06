<x-categories._poster_avatar :avatarPath="$thread->recentReply->poster->avatar_path">
</x-categories._poster_avatar>

<div class="ml-2">
    <a href="{{route('threads.show', $thread->slug) }}" class=" text-smaller font-extrabold tracking-wide">
        {{ $thread->shortTitle ?: '' }}
        <span class="text-xs text-gray-lightest font-hairline">...</span>
    </a>
    <div class="flex items-center justify-start text-smaller text-gray-lightest font-hairline">
        <p class="">{{ $thread->date_updated ?: '' }}
        </p>
        <p class="dot"></p>
        <p>{{ $thread->recentReply->poster->shortName ?: '' }}</p>
    </div>
</div>