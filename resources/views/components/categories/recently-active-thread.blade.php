<profile-popover :user="{{ $thread->recentReply->poster }}" trigger="avatar" trigger-classes="avatar-md">
</profile-popover>
<div class="ml-2 leading-snug flex-1">
    <p href="{{route('threads.show', $thread->slug) }}" class=" text-smaller font-extrabold tracking-wide">
        {{ $thread->shortTitle ?: '' }}
        <span class="text-xs text-gray-lightest font-hairline">...</span>
    </p>
    <div class="flex items-center justify-around text-smaller text-gray-lightest font-hairline">
        <p>{{ $thread->date_updated ?: '' }}
        </p>
        <p class="dot"></p>
        <profile-popover :user="{{ $thread->recentReply->poster }}" trigger-classes="text-smaller text-gray-lightest">
        </profile-popover>

    </div>
</div>