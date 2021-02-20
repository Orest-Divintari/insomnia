<x-layouts.forum>
    <div id="#Forum" class="flex section">
        <div class="w-4/5">
            @foreach($groups as $group)
            <div class="mb-5">
                <header class="mb-3 pb-4 border-b-6 border-blue-mid-dark">
                    <a class="text-blue-mid-dark text-xl tracking-wide font-insomnia" id="{{ $group->title }}"
                        href="#{{ $group->title }}">{{ $group->title }}
                    </a>
                    <p class="text-xs text-gray-lightest tracking-wide font-hairline">{{ $group->excerpt }}
                    </p>
                </header>
                <div>
                    @foreach($group->categories as $category)
                    <x-categories.list :category="$category" :loop="$loop">
                    </x-categories.list>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        <div class=" flex-1">
            @include('forum.latest-posts')
            @include('forum.statistics')
        </div>
    </div>
</x-layouts.forum>