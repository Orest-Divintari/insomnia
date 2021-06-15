<x-layouts.ignored ignoredType="members">
    <div class="border border-gray-ligghter rounded ">
        @forelse($ignoredUsers as $ignoredUser)
        <div class="flex p-3 {{ $loop->last ? 'border-b-0' : 'border-b' }}">
            <div class="flex flex-1 items-center">
                <img src="{{ $ignoredUser->avatar_path }}" class="avatar-lg" alt="">
                <div class="pl-7/2">
                    <a href="{{ route('profiles.show', $ignoredUser) }}" class="blue-link font-bold text-md">
                        {{ $ignoredUser->name }}
                    </a>
                    <div>
                        <p class="text-smaller"> Macrumors newbie </p>
                        <div class="flex text-gray-shuttle text-smaller">
                            <p>Messages: {{ $ignoredUser->profile_posts_count }}</p>
                            <p class="dot"></p>
                            <p>Like score: {{ $ignoredUser->received_likes_count }}</p>
                            <p class="dot"></p>
                            <p>Points: 0</p>
                        </div>
                    </div>
                </div>
            </div>
            <ignore-user-button :profile-owner="{{ $ignoredUser }}"
                :ignored="{{ json_encode($ignoredUser->ignored_by_visitor) }}">
            </ignore-user-button>

        </div>
        @empty
        <p class="text-md p-7/2">You are not currently ignoring any members.</p>
        @endforelse
    </div>
</x-layouts.ignored>