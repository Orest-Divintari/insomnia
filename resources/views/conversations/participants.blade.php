<div class="sidebar-block w-80 mt-5">
    <header class="sidebar-header">
        CONVERSATION PARTICIPANTS
    </header>
    @foreach($participants as $participant)
    <div class="text-smaller mt-1 pb-2 flex items-center">
        <img @click="showProfile( {{ $participant }} )" src="{{ $participant->avatarPath }}" class="avatar-md" alt="">
        <div class="ml-4 flex-1">
            <a @click="showProfile( {{ $participant }} )" class="blue-link">{{ $participant->name }}</a>
            <p class="text-gray-lightest ">macrumors newbie</p>
        </div>
        @if($participant->id != $conversation->starter->id)
        @can('manage', $conversation)
        <participant-settings :conversation="{{ $conversation }}" :participant="{{ $participant }}"
            class="self-baseline">
        </participant-settings>
        @endcan
        @endif
    </div>
    @endforeach

</div>