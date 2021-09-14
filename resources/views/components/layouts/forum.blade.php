<x-layouts.master>
    <x-slot name="navRight">
        <div class="flex">
            @guest
            <x-tab-header name="Login" destination="login"></x-tab-header>
            <x-tab-header name="Register" destination="register"></x-tab-header>
            @endguest


            @auth
            <div class="flex items-center">
                @role('admin')
                <div class="head-tab-item"> <a href="{{ route('admin.dashboard.index') }}"> <i class="fas fa-cog"> </i>
                    </a> </div>
                @endrole
                <profile-button :profile-owner="{{ auth()->user() }}"></profile-button>
                @verified
                <notifications-button></notifications-button>
                <conversations-button></conversations-button>
                @endverified

                <form action="{{ route('logout') }}" method="POST">
                    <button class="head-tab-item">Logout</button>
                </form>
            </div>

            @endauth


        </div>
    </x-slot>
    <x-slot name="subHeader">
        <div class="flex justify-between items-center px-5 bg-blue-light text-gray-900 ">
            <div class="flex ">
                <x-tab-subheader name="Home" destination="{{ route('forum') }}"></x-tab-subheader>
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
                        <a href="{{ route('threads.index') . '?posted_by=' . auth()->user()->name }}"
                            class=" dropdown-item hover:bg-white-catskill">Threads you started</a>
                        <a href="{{ route('threads.index') . '?contributed=' . auth()->user()->name }}"
                            class="dropdown-item">Threads you replied to</a>
                        <a href="{{ route('threads.index') . '?watched=true'}}" class="dropdown-item">Watched</a>
                        <a href="{{ route('recently-viewed-threads.index') }}" class="dropdown-item">History</a>
                    </template>
                </dropdown>
                @endauth
                <x-tab-subheader name="New Threads" destination="{{ route('threads.index') . '?new_threads=true'}}">
                </x-tab-subheader>
                <x-tab-subheader name="New Posts" destination="{{ route('threads.index') . '?new_posts=true'}}">
                </x-tab-subheader>
                <x-tab-subheader name="Unanswered" destination="{{ route('threads.index') . '?unanswered=true'}}">
                </x-tab-subheader>

                <x-tab-subheader name="Trending" destination="{{ route('threads.index') . '?trending=true' }}">
                </x-tab-subheader>
                @auth
                <dropdown>
                    <template v-slot:dropdown-trigger>
                        <div class="sub-head-tab-container">
                            <div class="px-5 border-r border-gray-400 flex">
                                <div href="" class="cursor-pointer mr-3/2">Members</div>
                                <i class="pt-1 fas fa-sort-down"></i>
                            </div>
                        </div>
                    </template>
                    <template v-slot:dropdown-items>
                        <a href="{{ route('online-user-activities.index') }}"
                            class=" dropdown-item hover:bg-white-catskill">Current visitors</a>
                        <a href="{{ route('profile-posts.index') . '?new_posts=true' }}"
                            class=" dropdown-item hover:bg-white-catskill">New profile posts</a>
                    </template>
                </dropdown>
                @endauth

            </div>
            <div class=" px-5">
                <forum-search></forum-search>
            </div>
        </div>
    </x-slot>
    <x-slot name="main">
        {{$slot}}
    </x-slot>
</x-layouts.master>