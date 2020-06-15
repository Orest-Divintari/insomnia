<x-layouts._forums>
    @foreach($subCategories as $category)
    <x-categories._list :category="$category" :loop="$loop"></x-categories._list>
    @endforeach
</x-layouts._forums>