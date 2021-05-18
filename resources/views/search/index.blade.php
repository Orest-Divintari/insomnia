<x-layouts.forum>
    @if($errors->any())
    <x-errors.title></x-errors.title>
    @foreach($errors->all() as $error)
    <p class="error-message-white"> {{ $error }} </p>
    @endforeach
    @elseif(is_string($results))
    <h1 class="text-3xl font-bold">Insomnia Forum</h1>
    <p class="error-message-white">{{ $results }}</p>
    @else
    <div class="py-12 -mx-5 -mt-5 bg-gallery"></div>
    <div class="section mb-1">
        @if(isset($query))
        <h1 class="text-3xl font-bold">Search results for query: <span class="text-blue-mid italic"> {{ $query }}
            </span> </h1>
        @else
        <h1 class="text-3xl font-bold">Search results</h1>
        @endif
    </div>
    <div class="section">
        <x-breadcrumb.container>
            <x-breadcrumb.item :title="'Forum'" :route="route('forum')"></x-breadcrumb.item>
            <x-breadcrumb.leaf :title="'Search'" :route="route('search.advanced')">
            </x-breadcrumb.leaf>
        </x-breadcrumb.container>
    </div>
    <search-results :dataset="{{ $results->toJson() }}" query="{{ $query }}"></search-results>
    @endif


</x-layouts.forum>