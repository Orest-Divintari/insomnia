<div class="sidebar-block w-80">
    <header class="sidebar-header">
        CONVERSATION INFO
    </header>
    <div class="text-smaller mt-1 pb-2">
        <div class="flex justify-between">
            <p class="text-gray-lightest">Participants: </p>
            <p>{{ $participants->count() }}</p>
        </div>
        <div class="flex justify-between">
            <p class="text-gray-lightest">Replies: </p>
            <p>{{ $messages->count() }}</p>
        </div>
        <div class="flex justify-between">
            <p class="text-gray-lightest">Last reply date: </p>
            <p>{{ $conversation->recentMessage->dateCreated }}</p>
        </div>
        <div class="flex justify-between">
            <p class="text-gray-lightest">Last reply from: </p>
            <p>{{ $conversation->recentMessage->poster->name }}</p>
        </div>
    </div>
</div>