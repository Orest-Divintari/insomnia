<x-layouts.master>
    <x-slot name="navRight">
        <search></search>
    </x-slot>
    <x-slot name="subHeader">
        to do
    </x-slot>
    <x-slot name="main">
        {{$slot}}
    </x-slot>
</x-layouts.master>