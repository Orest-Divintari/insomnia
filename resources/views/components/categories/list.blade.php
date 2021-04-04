<div
    class="{{ $loop->even ? 'bg-blue-lighter' : 'bg-white' }} border border-white-catskill {{ ($loop->first) ? '' : 'border-t-0' }} py-2 pl-2">
    <div class="flex items-center">
        <img src="{{ $category->avatar_path }}" alt="category_avatar" class="avatar-lg mr-3">
        <div class="flex-1">
            <a class="text-sm text-blue-mid-dark font-semibold tracking-wide hover:underline"
                href="{{ route('categories.show',$category->slug) }}">{{ $category->title }}</a>
            @if($category->hasSubCategories())
            <div class=" flex">
                @foreach($category->subCategories as $subCategory)
                <a href="{{ route('categories.show',$subCategory->slug) }}" class="mr-4"> <i
                        class="text-sm far fa-comment-dots"></i>
                    <span
                        class="text-smaller font-black font-hairline hover:text-blue-mid-dark hover:underline">{{ $subCategory->title }}</span></a>
                @endforeach
            </div>
            @else
            <p class="text-xs text-gray-lightest font-hairline tracking-wide">
                {{ $category->excerpt }}
            </p>
            @endif
        </div>
        <div class="flex items-center ">
            <x-categories.statistics :threadsCount="$category->threads_count" :replies_count="$category->replies_count">
            </x-categories.statistics>
        </div>
        <div class="w-72 flex items-center justify-start ml-2">
            <x-categories.recently-active-thread :thread="$category->recentlyActiveThread">
            </x-categories.recently-active-thread>
        </div>


    </div>
</div>