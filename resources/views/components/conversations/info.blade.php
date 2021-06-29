<div class="sidebar-block w-80 leading-relaxed" dusk="conversation-info">
    <header class="sidebar-header">
        CONVERSATION INFO
    </header>
    <div class="text-smaller mt-1 pb-2">
        <div class="flex justify-between ">
            <p class="text-gray-lightest">Participants: </p>
            <p>{{ $conversation->participants_count }}</p>
        </div>
        <div class="flex justify-between ">
            <p class="text-gray-lightest">Replies: </p>
            <p>{{ $conversation->messages_count }}</p>
        </div>
        <div class="flex justify-between ">
            <p class="text-gray-lightest">Last reply date: </p>
            <p>{{ $conversation->recentMessage->dateCreated }}</p>
        </div>
        <div class="flex justify-between ">
            <p class="text-gray-lightest">Last reply from: </p>
            <a class="blue-link"
                @click="showProfile( {{ $conversation->recentMessage->poster }} )">{{ $conversation->recentMessage->poster->name }}</a>
        </div>
    </div>
</div>