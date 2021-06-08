<x-layouts.account title="Notifications" section="notifications">
    <x-slot name="main">
        <notifications :user="{{ auth()->user() }}" :dataset="{{ json_encode($notifications) }}"></notifications>
    </x-slot>
</x-layouts.account>