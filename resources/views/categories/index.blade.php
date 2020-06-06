<x-layouts._forums>
    <div class="flex section">
        <div class="w-4/5">
            @foreach($groups as $group)
            <div class="mb-5">
                <header class="mb-3 pb-4 border-b-6 border-blue-mid-dark">
                    <h1 class="text-blue-mid-dark text-xl tracking-wide font-insomnia">News Article and Discussion
                    </h1>
                    <p class="text-xs text-gray-lightest tracking-wide font-hairline">News, rumors, guides and how tos
                    </p>
                </header>
                @foreach($group->parentCategories as $category)
                <div
                    class="{{ $loop->even ? 'bg-blue-form-side' : 'bg-white' }} border border-blue-border {{ ($loop->first) ? '' : 'border-t-0' }} py-3 pl-3">

                    <div class="flex items-center">
                        <img src="{{ $category->avatar_path }}" alt="category_avatar"
                            class="w-12 h-12 mr-3 rounded-full object-cover">
                        <div class="flex-1 flex flex-col">
                            <a class="self-start text-sm text-blue-mid-dark font-semibold tracking-wide"
                                href="">Macrumors and
                                discussion</a>
                            @if($category->children)
                            <div class="flex">
                                @foreach($category->children as $children)

                                <a href="" class="mr-4"> <i class="text-sm far fa-comment-dots"></i>
                                    <span
                                        class="text-smaller font-black font-hairline">{{ $children->title }}</span></a>
                                @endforeach
                            </div>
                            @else
                            <p class="text-xs text-gray-lightest font-hairline tracking-wide">
                                {{ $category->excerpt }}
                            </p>
                            @endif
                        </div>
                        <div class="flex flex-col items-center border-r border-gray-400 pr-3">
                            <p class="text-sm tracking-wide">{{$category->threads_count}}</p>
                            <p class="text-gray-lightest text-xs font-hairline"> Threads </p>
                        </div>
                        <div>
                            <div class="flex flex-col items-center pl-3">
                                <p class="text-sm tracking-wide">{{$category->threads_count}}</p>
                                <p class="text-gray-lightest text-xs font-hairline"> Messages </p>
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <img src="{{ user_avatar() }}" class="" alt="user_avatar">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach

        </div>
        <div class="flex-1">
            latest posts
        </div>
    </div>

    <!-- @foreach($groups as $group)
    <p>{{ $group->title }}</p>
    @foreach($group->parentCategories as $category)

    <a href="/forum/categories/{{$category->slug}}">{{ $category->title }}</a>
    @endforeach
    @endforeach -->

</x-layouts._forums>