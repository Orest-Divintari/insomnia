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
        <div class="flex items-center ">
            @if($category->hasSubCategories())
            <x-categories._statistics :threadsCount="$category->parent_category_threads_count"
                :replies_count="$category->parent_category_replies_count"></x-categories._statistics>
            @else
            <x-categories._statistics :threadsCount="$category->threads_count"
                :replies_count="$category->replies_count"></x-categories._statistics>
            @endif
        </div>
        <div class="w-72 flex items-center justify-start ml-2">
            @if($category->hasSubCategories())
            <x-categories._recently_active_thread :recentlyActiveThread="$category->parentCategoryRecentlyActiveThread">
            </x-categories._recently_active_thread>
            @else
            <x-categories._recently_active_thread :recentlyActiveThread="$category->recentlyActiveThread">
            </x-categories._recently_active_thread>
            @endif
        </div>


    </div>
</div>