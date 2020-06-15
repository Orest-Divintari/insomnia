<div class="flex flex-col items-start pr-2">
    <a href="{{route('threads.show', $recentlyActiveThread->slug) }}"
        class="w-56 text-smaller font-extrabold tracking-wide">
        {{ rtrim(Str::limit($recentlyActiveThread->title,24,'')) }}
        <span class="text-xs text-gray-lightest font-hairline">...</span>
    </a>
    <div class="flex items-center justify-start text-smaller text-gray-lightest font-hairline">

        <p class="">{{ $recentlyActiveThread->updated_at->diffForHumans() }}
        </p>
        <p class="bg-gray-600  w-1 h-1 rounded-full mx-2"> </p>
        @if(isset($recentlyActiveThread->recentReply))
        <p>{{ Str::limit($recentlyActiveThread->recentReply->poster->name, 20, '') }}</p>
        @else
        <p>{{ Str::limit($recentlyActiveThread->poster->name, 20, '') }}</p>
        @endif
    </div>
</div>