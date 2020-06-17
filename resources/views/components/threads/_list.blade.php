@foreach($category->threads as $thread)
<div
    class="{{ $loop->even ? 'bg-blue-light' : 'bg-white' }} border border-blue-border {{ ($loop->first) ? '' : 'border-t-0' }} ">
    <div class="flex items-center">
        <div class="py-5/2 px-5">
            <img src="{{ $thread->poster->avatar_path }}" class="w-9 h-9 avatar" alt="">

        </div>
        <div class="p-5/2 flex-1">
            <a href="route('threads.show', $thread)" class="text-sm hover:underline hover:text-blue-mid font-bold">
                {{$thread->title}}
            </a>
            <div class="flex items-center">
                <a class="text-xs text-gray-lightest leading-none hover:unerline ">
                    {{ $thread->poster->shortName }} </a>
                <p class="dot"></p>
                <a href="{{ route('threads.show', $thread) }} " class="hover:underline text-xs text-gray-lightest ">
                    {{ $thread->date_created }}</a>
                <p></p>
            </div>
        </div>
        <div class="p-5/2 text-gray-lightest">
            <div class="flex justify-between items-end">
                <p class="text-sm flex-1">Replies:</p>
                <p class="text-xs text-black"> {{ $thread->replies_count }}</p>
            </div>
            <div class="flex justify-between items-end ">
                <p class="text-sm ">Views:</p>
                <p class="text-xs ">2</p>
            </div>
        </div>
        <div class="p-5/2 mr-5">
            @if (isset($thread->recentReply))
            <x-threads._latest_activity :item="$thread->recentReply">
            </x-threads._latest_activity>
            @else
            <x-threads._latest_activity :item="$thread">
            </x-threads._latest_activity>
            @endif

        </div>
        <div></div>

    </div>
</div>
@endforeach