<x-layouts._forums>
    <div class="flex section">
        <div class="w-4/5">
            @foreach($groups as $group)
            <div class="mb-5">
                <header class="mb-3 pb-4 border-b-6 border-blue-mid-dark">
                    <a class="text-blue-mid-dark text-xl tracking-wide font-insomnia" id="{{ $group->title }}"
                        href="#{{ $group->title }}">News Article and Discussion
                    </a>
                    <p class="text-xs text-gray-lightest tracking-wide font-hairline">News, rumors, guides and how tos
                    </p>
                </header>
                @foreach($group->categories as $category)
                <x-categories._list :category="$category" :loop="$loop">
                </x-categories._list>
                @endforeach
            </div>
            @endforeach
        </div>
        <div class=" flex-1">
            <div class="ml-5 border-t-8 border border-gray-lighter">
                <header class="p-5/2 tracking-wide font-hairline text-gray-mid">
                    LATEST POSTS
                </header>
            </div>
        </div>
    </div>
</x-layouts._forums>