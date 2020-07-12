<x-layouts.master>
    <x-slot name="navRight">
        <div class="flex">
            @guest
            <x-head_tab_item name="Login" destination="login"></x-head_tab_item>
            <x-head_tab_item name="Register" destination="register"></x-head_tab_item>
            @endguest
            @auth
            <div class="flex items-center">
                <div class="flex items-center head-tab-item">
                    <img src="{{ auth()->user()->avatar_path }}" class="avatar-sm mr-1" alt="">
                    <p> {{ auth()->user()->name  }} </p>
                </div>
                <notification></notification>
            </div>
            @endauth

        </div>
    </x-slot>
    <x-slot name="subHeader">
        <div class="flex justify-between items-center px-5 bg-blue-light text-gray-900 ">
            <div class="flex ">
                <x-sub_head_tab_item name="Home" destination='forum'></x-sub_head_tab_item>
                <x-sub_head_tab_item name="New Posts" destination='home'></x-sub_head_tab_item>
                <x-sub_head_tab_item name="Support" destination='home'></x-sub_head_tab_item>
                <x-sub_head_tab_item name="My Threads" destination="{{ route('filtered-threads.index') . '?by=' . auth()->user()->name }}"></x-sub_head_tab_item>

            </div>
            <div class=" px-5">Search
            </div>
        </div>
    </x-slot>
    <x-slot name="main">
        {{$slot}}
    </x-slot>
</x-layouts.master>
