<div
    class="{{ $loop->even ? 'bg-blue-form-side' : 'bg-white' }} border border-blue-border {{ ($loop->first) ? '' : 'border-t-0' }} py-2 pl-2">
    <div class="flex items-center">
        <img src="{{ $category->avatar_path }}" alt="category_avatar" class="w-12 h-12 mr-3 rounded-full object-cover">
        <div class="flex-1">
            <a class="text-sm text-blue-mid-dark font-semibold tracking-wide"
                href="{{ route('forum.categories.show',$category->slug) }}">{{ $category->title }}</a>
            @if($category->hasSubCategories())
            <div class=" flex">
                @foreach($category->subCategories as $subCategory)
                <a href="{{ route('forum.categories.show',$subCategory->slug) }}" class="mr-4"> <i
                        class="text-sm far fa-comment-dots"></i>
                    <span class="text-smaller font-black font-hairline">{{ $subCategory->title }}</span></a>
                @endforeach
            </div>
            @else
            <p class="text-xs text-gray-lightest font-hairline tracking-wide">
                {{ $category->excerpt }}
            </p>
            @endif
        </div>
        <div class="flex mr-3 items-center ">
            <div class="flex flex-col items-center border-r border-gray-400 pr-3">
                <p class="text-sm tracking-wide">{{ $category->threads_count }}</p>
                <p class="text-gray-lightest text-xs font-hairline"> Threads </p>
            </div>
            <div class="flex flex-col items-center px-5">
                <p class="text-sm tracking-wide">{{ $category->replies_count }}</p>
                <p class="text-gray-lightest text-xs font-hairline"> Messages </p>
            </div>

        </div>

        @if($category->hasSubCategories())
        <x-categories._recently_active_thread :recentlyActiveThread="$category->parentCategoryRecentlyActiveThread">
        </x-categories._recently_active_thread>
        @else
        <x-categories._recently_active_thread :recentlyActiveThread="$category->recentlyActiveThread">
        </x-categories._recently_active_thread>
        @endif



    </div>
</div>