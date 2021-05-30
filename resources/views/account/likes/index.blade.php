<x-layouts.account section="likes" title="Likes Received">
    <x-slot name="main">
        <div class="border border-gray-lighter rounded">
            @forelse($likes as $like)
            <div class="p-5/2 {{ $loop->last ? 'border-b-0' : 'border-b' }}">
                <div class="flex items-center">
                    <profile-popover :user="{{ $like->liker }}" trigger="avatar"
                        trigger-classes="avatar-lg rounded-full">
                    </profile-popover>
                    @include("account.likes.{$like->type}")
                </div>
            </div>
            @empty
            <p class="p-7/2  text-sm text-black-semi">You haven't received any likes yet.</p>
            @endforelse
        </div>
        <paginator :dataset="{{ json_encode($likes) }}"></paginator>
    </x-slot>
</x-layouts.account>