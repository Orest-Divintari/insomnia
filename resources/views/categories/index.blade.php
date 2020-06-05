<x-layouts._forums>
    @foreach($groups as $group)
    <p>{{ $group->title }}</p>
    @foreach($group->parentCategories as $category)

    <a href="/forum/categories/{{$category->slug}}">{{ $category->title }}</a>
    @endforeach
    @endforeach

</x-layouts._forums>