<div class="sidebar-block">
    <header class="sidebar-header">
        LATEST POSTS
    </header>
    <div class="flex flex-col">
        @forelse($latestPosts as $latestPost)

        <div class="flex p-5/2 items-start">
            <img class="h-6 w-6 rounded-full object-cover mt-1"
                src="{{ $latestPost->recentReply->poster->avatar_path }}" alt="">
            <div class="pl-5/2">
                <a class="text-sm tacking-wide leading-normal font-bold text-blue-mid-dark hover:underline"
                    href="{{ route('threads.show', $latestPost) }}">{{ $latestPost->title }}</a>
                <div class="flex text-smaller text-gray-lightest items-center">
                    <p class="mr-1">Latest:</p>
                    <p>{{ $latestPost->recentReply->poster->shortName }}</p>
                    <p class="dot"></p>
                    <p> {{ $latestPost->date_updated }} </p>
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