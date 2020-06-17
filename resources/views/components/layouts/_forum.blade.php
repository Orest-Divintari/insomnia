<x-layouts.master>
    <x-slot name="navRight">
        <div class="flex ">
            @guest
            <x-head_tab_item name="Login" destination="login"></x-head_tab_item>
            <x-head_tab_item name="Register" destination="register"></x-head_tab_item>
            @endguest
        </div>
    </x-slot>
    <x-slot name="subHeader">
        <div class="flex justify-between items-center px-5 bg-blue-lighter text-gray-900 ">
            <div class="flex ">
                <x-sub_head_tab_item name="Home" destination='forum'></x-sub_head_tab_item>
                <x-sub_head_tab_item name="New Posts" destination='home'></x-sub_head_tab_item>
                <x-sub_head_tab_item name="Support" destination='home'></x-sub_head_tab_item>
            </div>
            <div class="px-5">Search</div>
        </div>
    </x-slot>
    <x-slot name="main">
        {{$slot}}
    </x-slot>
</x-layouts.master>
