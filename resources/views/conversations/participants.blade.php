<div class="sidebar-block w-80 mt-5">
    <header class="sidebar-header">
        CONVERSATION PARTICIPANTS
    </header>
    @foreach($participants as $participant)
    <div class="text-smaller mt-1 pb-2 flex">
        <img src="{{ $participant->avatarPath }}" class="avatar-md" alt="">
        <div class="ml-4">
            <a class="blue-link "> {{ $participant->name }} </a>
            <p class="text-gray-lightest ">macrumors newbie</p>
        </div>
    </div>
    @endforeach

</div>