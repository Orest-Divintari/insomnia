@if(isset($recentlyActiveThread->recentReply))
<x-categories._poster_avatar :avatarPath="$recentlyActiveThread->recentReply->poster->avatar_path">
</x-categories._poster_avatar>
@else
<x-categories._poster_avatar :avatarPath="$recentlyActiveThread->poster->avatar_path"></x-categories._poster_avatar>
@endif
<div class="ml-2">
    <a href="{{route('threads.show', $recentlyActiveThread->slug) }}"
        class=" text-smaller font-extrabold tracking-wide">
        {{ $recentlyActiveThread->shortTitle }}
        <span class="text-xs text-gray-lightest font-hairline">...</span>
    </a>
    <div class="flex items-center justify-start text-smaller text-gray-lightest font-hairline">
        <p class="">{{ $recentlyActiveThread->updated_at->diffForHumans() }}
        </p>
        <p class="bg-gray-600  w-1 h-1 rounded-full mx-2"> </p>
        @if(isset($recentlyActiveThread->recentReply))
        <p>{{ $recentlyActiveThread->recentReply->poster->shortName}}</p>
        @else
        <p>{{ $recentlyActiveThread->poster->shortName }}</p>
        @endif
    </div>
</div>