<x-layouts._forum>
    <header>
        <a href="#{{ $category->title }}" class="section-title"> {{ $category->title }} </a>
    </header>
    <main class="section">

        <x-breadcrumb.container>
            <x-breadcrumb.item :title="'Forum'" :route="route('forum')"></x-breadcrumb.item>
            <x-breadcrumb.leaf :title="$category->group->title" :route="route('forum')">
            </x-breadcrumb.leaf>
        </x-breadcrumb.container>

        <div>
            @foreach($subCategories as $category)
            <x-categories._list :category="$category" :loop="$loop"></x-categories._list>
            @endforeach
        </div>
    </main>
</x-layouts._forum>
