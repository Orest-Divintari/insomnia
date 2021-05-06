<x-layouts.account section="following" title="Following">
    <x-slot name="main">
        @if(!empty($followingUsers->items()))
        <div class="border rounded border-white-catskill" v-cloak>
            @foreach($followingUsers as $user)
            <div class="flex items-center {{ !$loop->last ? 'border-b' : '' }} border-white-catstkill p-7/2">
                <a href="{{ route('profiles.show', $user) }}"><img src="{{ $user->avatar_path }}" class="avatar-lg"
                        alt=""></a>
                <div class="flex-1 pl-7/2">
                    <a href="{{ route('profiles.show', $user) }}" class="blue-link text-md font-semibold">
                        {{ $user->name }}</a>
                    <p class="text-black-semi text-smaller">Macrumors newbie</p>
                    <div class="flex leading-3 items-center text-gray-lightest text-smaller ">
                        <div class="flex items-center leading-4">
                            <p class="mr-1">Messages:</p>
                            <p>{{ $user->messages_count }}</p>
                        </div>
                        <p class="dot"></p>
                        <div class="flex items-center">
                            <p class="mr-1">Likes score:</p>
                            <p>{{ $user->likes_count }}</p>
                        </div>
                        <p class="dot"></p>
                        <div class="flex items-center">
                            <p class="mr-1">Points:</p>
                            <p> 0 </p>
                        </div>
                    </div>
                </div>
                <follow-button class="self-start" :followed="true" :profile-owner="{{ $user }}">
                </follow-button>
            </div>

            @endforeach



        </div>
        <paginator :dataset="{{ json_encode($followingUsers) }}"> </paginator>

        @else
        <p class="p-7/2 border rounded border-white-catskill text-sm text-black-semi"> You are not currently following
            any
            members. </p>
        @endif

    </x-slot>
</x-layouts.account>