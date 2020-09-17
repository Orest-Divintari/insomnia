<x-layouts._forum>
    <div class="py-12 -mx-5 -mt-5 bg-gallery"></div>
    <div class="section">
        <h1 class="text-3xl font-bold">Search results for query: <span class="text-blue-mid italic"> {{ $query }}
            </span></h1>
    </div>
    <div class="section">
        <x-breadcrumb.container>
            <x-breadcrumb.item :title="'Forum'" :route="route('forum')"></x-breadcrumb.item>
            <x-breadcrumb.leaf :title="'Search'" :route="route('search.advanced')"></x-breadcrumb.leaf>
        </x-breadcrumb.container>
    </div>
    @if(is_string($results))
    <h1 class="section-title">{{ $results }}</h1>
    @else
    <search-results :dataset="{{ $results->toJson() }}"></search-results>
    @endif

</x-layouts._forum>