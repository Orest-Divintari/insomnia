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
                <x-sub_head_tab_item name="Home" destination="{{ route('forum') }}"></x-sub_head_tab_item>
                @auth
                <dropdown>
                    <template v-slot:dropdown-trigger>
                        <div class="sub-head-tab-container">
                            <div class="px-5 border-r border-gray-400 flex">
                                <div href="" class="cursor-pointer mr-3/2">Your content</div>
                                <i class="pt-1 fas fa-sort-down"></i>
                            </div>
                        </div>
                    </template>
                    <template v-slot:dropdown-items>
                        <a href="{{ route('filtered-threads.index') . '?by=' . auth()->user()->name }}"
                            class=" dropdown-item hover:bg-white-catskill">Threads you started</a>
                        <a href="{{ route('filtered-threads.index') . '?participatedBy=' . auth()->user()->name }}"
                            class="dropdown-item">Threads you replied to</a>
                    </template>
                </dropdown>
                @endauth
                <x-sub_head_tab_item name="New Threads"
                    destination="{{ route('filtered-threads.index') . '?newThreads=1'}}"></x-sub_head_tab_item>
                <x-sub_head_tab_item name="New Posts"
                    destination="{{ route('filtered-threads.index') . '?newPosts=1'}}">
                </x-sub_head_tab_item>
                <x-sub_head_tab_item name="Unanswered"
                    destination="{{ route('filtered-threads.index') . '?unanswered=1'}}">
                </x-sub_head_tab_item>

                <x-sub_head_tab_item name="Trending"
                    destination="{{ route('filtered-threads.index') . '?trending=1' }}">
                </x-sub_head_tab_item>

            </div>
            <div class=" px-5">Search
            </div>
        </div>
    </x-slot>
    <x-slot name="main">
        {{$slot}}
    </x-slot>
</x-layouts.master>