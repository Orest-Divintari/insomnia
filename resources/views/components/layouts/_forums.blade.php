<x-layouts.master>
    <x-slot name="navRight">
        <div class="flex ">
            @guest
            <x-head-tab-item name="Login" destination="login"></x-head-tab-item>
            <x-head-tab-item name="Register" destination="register"></x-head-tab-item>
            @endguest
        </div>
    </x-slot>
    <x-slot name="subHeader">
        <div class="flex justify-between items-center px-5 bg-blue-light text-gray-900 ">
            <div class="flex ">
                <x-sub-head-tab-item name="Home" destination='home'></x-sub-head-tab-item>
                <x-sub-head-tab-item name="New Posts" destination='home'></x-sub-head-tab-item>
                <x-sub-head-tab-item name="Support" destination='home'></x-sub-head-tab-item>
            </div>
            <div class="px-5">Search</div>
        </div>
    </x-slot>
    <x-slot name="main">
        {{$slot}}
    </x-slot>
</x-layouts.master>