<x-layouts._forums>

    @foreach($categories as $category)

    <a href="/forum/categories/{{$category->slug}}">{{ $category->title }}</a>
    @endforeach

</x-layouts._forums>