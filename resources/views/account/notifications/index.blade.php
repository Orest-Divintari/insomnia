<x-layouts.account title="Alerts" section="alerts">
    <x-slot name="main">
        <notifications :user="{{ $user }}" :dataset="{{ json_encode($notifications) }}"></notifications>
    </x-slot>
</x-layouts.account>