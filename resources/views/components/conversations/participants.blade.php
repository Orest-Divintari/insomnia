<div class="sidebar-block w-80 mt-5" dusk="conversation-participants">
    <header class="sidebar-header">
        CONVERSATION PARTICIPANTS
    </header>
    @foreach($participants as $participant)
    <div class="text-smaller mt-1 pb-2 flex items-center">
        <profile-popover :user="{{ $participant }}" trigger="avatar" trigger-classes="avatar-md"></profile-popover>
        <div class="ml-4 flex-1">
            <profile-popover :user="{{ $participant }}" trigger-classes="blue-link"></profile-popover>
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