<div class="mt-5 sidebar-block">
    <header class="sidebar-header">
        FORUM STATISTICS
    </header>
    <div class="text-sm ">
        <div class="flex justify-between">
            <p class="text-gray-lightest">Threads: </p>
            <p>{{ $statistics['threads_count'] }}</p>
        </div>
        <div class="flex justify-between">
            <p class="text-gray-lightest">Messages: </p>
            <p>{{ $statistics['thread_replies_count'] }}</p>
        </div>
        <div class="flex justify-between">
            <p class="text-gray-lightest">Members: </p>
            <p>{{ $statistics['users_count'] }}</p>
        </div>
    </div>
</div>