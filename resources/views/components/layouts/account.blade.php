<x-layouts.forum>

    <header>
        <h1 class="section-title "> {{ $title }} </h1>
    </header>

    <main class="section">
        <x-breadcrumb.container>
            <x-breadcrumb.item :title="'Forum'" :route="route('forum')"></x-breadcrumb.item>
            <x-breadcrumb.leaf :jumpToContent="'false'" :title="'Your Account'" :route="route('account')">
            </x-breadcrumb.leaf>
        </x-breadcrumb.container>
        <div class="flex">
            <div class="w-1/5 border border-gray-lighter border-t-8 text-gray-shuttle">
                <header class="p-5/2 text-md border-b border-gray-lighter">
                    YOUR ACCOUNT
                </header>
                <div class="flex flex-col text-sm">
                    <a class="py-2 px-4 {{ $section == 'profile' ? 'text-blue-mid border-l-2 border-blue-mid font-bold bg-white-smoke' : 'hover:bg-white-smoke hover:text-black-semi' }}"
                        href=""> Your
                        profile
                    </a>
                    <a class="py-2 px-4 {{ $section == 'alerts' ? 'text-blue-mid border-l-2 border-blue-mid font-bold bg-white-smoke' : 'hover:bg-white-smoke hover:text-black-semi' }}"
                        href=""> Alerts </a>
                    <a class="py-2 px-4 {{ $section == 'likes' ? 'text-blue-mid border-l-2 border-blue-mid font-bold bg-white-smoke' : 'hover:bg-white-smoke hover:text-black-semi' }}"
                        href=""> Likes received </a>
                    <a class="py-2 px-4 {{ $section == 'bookmarks' ? 'text-blue-mid border-l-2 border-blue-mid font-bold bg-white-smoke' : 'hover:bg-white-smoke hover:text-black-semi' }}"
                        href=""> Bookmarks </a>
                    <a class="py-2 px-4 {{ $section == 'shipping address' ? 'text-blue-mid border-l-2 border-blue-mid font-bold bg-white-smoke' : 'hover:bg-white-smoke hover:text-black-semi' }}"
                        href=""> Shipping address </a>

                </div>
                <div>
                    <h1 class="text-md p-5/2  border-t border-gray-lighter">SETTINGS </h1>
                    <div class="flex flex-col text-sm">
                        <a class="py-2 px-4 {{ $section == 'details' ? 'text-blue-mid border-l-2 border-blue-mid font-bold bg-white-smoke' : 'hover:bg-white-smoke hover:text-black-semi'  }}"
                            href="{{ route('account.details.edit') }}"> Account details </a>

                        <a class="py-2 px-4 {{ $section == 'password and security' ? 'text-blue-mid border-l-2 border-blue-mid font-bold bg-white-smoke' : 'hover:bg-white-smoke hover:text-black-semi' }}"
                            href=""> Password and security
                        </a>

                        <a class="py-2 px-4 {{ $section == 'privacy' ? 'text-blue-mid border-l-2 border-blue-mid font-bold bg-white-smoke' : 'hover:bg-white-smoke hover:text-black-semi' }}"
                            href=""> Privacy </a>
                        <a class="py-2 px-4 {{ $section == 'preferences' ? 'text-blue-mid border-l-2 border-blue-mid font-bold bg-white-smoke' : 'hover:bg-white-smoke hover:text-black-semi' }}"
                            href=""> Preferences </a>
                        <a class="py-2 px-4 {{ $section == 'connected accounts' ? 'text-blue-mid border-l-2 border-blue-mid font-bold bg-white-smoke' : 'hover:bg-white-smoke hover:text-black-semi' }}"
                            href=""> Connected accounts
                        </a>

                        <a class="py-2 px-4 {{ $section == 'following' ? 'text-blue-mid border-l-2 border-blue-mid font-bold bg-white-smoke' : 'hover:bg-white-smoke hover:text-black-semi' }}"
                            href="{{ route('account.follows.index') }}"> Following </a>
                        <a class="py-2 px-4 {{ $section == 'ignoring' ? 'text-blue-mid border-l-2 border-blue-mid font-bold bg-white-smoke' : 'hover:bg-white-smoke hover:text-black-semi' }}"
                            href=""> Ignoring </a>
                        <a class="py-2 px-4 {{ $section == 'log out' ? 'text-blue-mid border-l-2 border-blue-mid font-bold bg-white-smoke' : 'hover:bg-white-smoke hover:text-black-semi' }}"
                            href=""> Log out </a>
                    </div>
                </div>
            </div>
            <div class="w-4/5 ml-6">
                {{ $main }}
            </div>
        </div>
    </main>

</x-layouts.forum>