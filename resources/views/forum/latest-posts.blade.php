<div class="sidebar-block">
    <header class="sidebar-header">
        LATEST POSTS
    </header>
    <div class="flex flex-col">
        @forelse($latestPosts as $latestPost)

        <div class="flex p-5/2 items-start">
            <profile-popover :user=" {{ $latestPost->recentReply->poster }} " trigger="avatar"
                trigger-classes="avatar-sm mt-1">
            </profile-popover>

            <div class="pl-5/2 flex-1">
                <a class="text-sm tacking-wide leading-normal font-bold text-blue-mid-dark hover:underline"
                    href="{{ route('threads.show', $latestPost) }}">{{ $latestPost->title }}</a>
                <div class="text-smaller text-gray-lightest items-center w-60">
                    <span class="mr-1 inline">Latest:</span>
                    <profile-popover :user="{{ $latestPost->recentReply->poster }}" class="inline"
                        popover-classes="inline" trigger-classes="tet-smaller text-gray-lightest pt-1">
                    </profile-popover>
                    <span class="dot inline-block mb-1"></span>
                    <a href="{{ route('replies.show', $latestPost->recentReply) }}"
                        class="inline cursor-pointer hover:underline">
                        {{ $latestPost->updated_at->diffForHumans() }} </a>
                </div>
                <a class="text-smaller text-gray-lightest underline"
                    href="{{ route('categories.show', $latestPost->category->slug) }}">
                    {{ $latestPost->category->title }} </a>
                </p>
            </div>
        </div>
        @empty
        <p>No posts yet</p>
        @endforelse
    </div>
</div>