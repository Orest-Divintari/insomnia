<x-layouts.account title="Notifications" section="notifications">
    <x-slot name="main">
        @if(!empty($notifications->items()))
        <div class="border border-gray-lighter rounded" v-cloak>
            @foreach($notifications as $notification)
            <notification :notification="{{ json_encode($notification) }}"
                class="p-7/2 text-sm  text-black-semi {{ $notification->is_read ? 'bg-white' : 'bg-white-catskill'}} {{ $loop->last ? 'border-b-0' : 'border-b' }}">
            </notification>
            @endforeach
        </div>
        <paginator :dataset="{{ json_encode($notifications) }}"></paginator>
        @else
        <p class="border border-gray-lighter rounded p-7/2 text-sm">
            You do not have any recent notifications.
        </p>
        @endif
    </x-slot>
</x-layouts.account>