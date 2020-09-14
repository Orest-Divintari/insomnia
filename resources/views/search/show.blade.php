<x-layouts._forum>
    <x-breadcrumb.container>
        <x-breadcrumb.item :title="'Forum'" :route="route('forum')"></x-breadcrumb.item>
        <x-breadcrumb.leaf :title="'Search'" :route="route('search.advanced')"></x-breadcrumb.leaf>
    </x-breadcrumb.container>

    @foreach ($results as $result)
    {{ $result->name }}
    @endforeach


</x-layouts._forum>