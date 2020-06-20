<x-layouts._forum>
    @if(!empty($category))
    <header class="flex justify-between">
        <div>
            <h1 class="section-title">
                {{ $category->title }}
            </h1>

            <p class="text-smaller text-gray-lightest">{{ $category->excerpt }}</p>
        </div>
        @auth
        <div class="py-4">
            <a href="{{route('threads.create', $category->id)}}" class="btn-post"><span
                    class="fas fa-pen text-white text-xs mr-1"></span>Post
                Thread</a>
        </div>
        @endauth
    </header>
    @endif
    <main class="section">

        @if(!empty($category))
        <x-breadcrumb.container>
            <x-breadcrumb.item :title="'Forum'" :route="route('forum')"></x-breadcrumb.item>
            <x-breadcrumb.item :title="$category->group->title" :route="route('forum')"></x-breadcrumb.item>
            <x-breadcrumb.leaf :title="$category->category->title"
                :route="route('categories.show', $category->category->slug)">
            </x-breadcrumb.leaf>
        </x-breadcrumb.container>
        @endif

        <threads :threads="{{ $threads->toJson()}}"></threads>
    </main>


</x-layouts._forum>