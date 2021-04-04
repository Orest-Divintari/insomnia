<profile-popover :user="{{ $thread->recentReply->poster }}" trigger="avatar" trigger-classes="avatar-md">
</profile-popover>
<div class="ml-2 leading-snug flex-1 truncate mr-2">
    <a href="{{ route('threads.show', $thread->slug) }}"
        class="block text-smaller font-extrabold tracking-wide hover:text-blue-mid-dark hover:underline truncate">
        {{ $thread->title ?: '' }}
    </a>
    <div class="flex items-center justify-start text-smaller text-gray-lightest font-hairline">
        <p>{{ $thread->date_updated ?: '' }}
        </p>
        <p class="dot"></p>
        <profile-popover class="min-w-0" :user="{{ $thread->recentReply->poster }}"
            trigger-classes="text-smaller text-gray-lightest">
        </profile-popover>

    </div>
</div>