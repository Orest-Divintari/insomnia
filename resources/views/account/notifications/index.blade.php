<x-layouts.account title="Notifications" section="notifications">
    <x-slot name="main">
        <account-notifications :user="{{ auth()->user() }}" :dataset="{{ json_encode($notifications) }}">
        </account-notifications>
    </x-slot>
</x-layouts.account>