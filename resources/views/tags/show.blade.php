<x-layouts.forum>
    <header>
        <h1 class="section-title">{{ $tag->name }}</h1>
    </header>
    <main class="section">
        <x-breadcrumb.container>
            <x-breadcrumb.item :title="'Forum'" :route="route('forum')"></x-breadcrumb.item>
            <x-breadcrumb.leaf :title="'Tags'" :route="'/search/advanced?type=tags'">
            </x-breadcrumb.leaf>
        </x-breadcrumb.container>

        @foreach($tag->threads as $thread)
        <div
            class="flex border rounded  {{ $loop->last ? 'border-b-1 rounded-b rounded-t-none' : 'border-b-0 rounded-b-none rounded-t-none' }} {{ $loop->first ? 'rounded-t' : '' }} border-gray-lighter p-7/2">
            <img src="{{ $thread->poster->avatar_path }}" class="avatar-lg mr-3">
            <div>
                <a class="blue-link text-md" href="{{ route('threads.show', $thread) }}">{{ $thread->title }}</a>
                <p class="mt-1/2 text-black-semi text-smaller"> {{  strip_tags($thread->body) }} </p>
                <div class="mt-1/2 flex items-center text-gray-lightest text-smaller">
                    <a href="/profiles/{{ $thread->poster->name }}" class="underline"> {{ $thread->poster->name }} </a>
                    <p class="dot"></p>
                    <p>Thread</p>
                    <p class="dot"></p>
                    <p class="text-gray-700"> {{ $thread->date_created }} </p>
                    <p class="dot mr-1/2"></p>
                    <div class="flex items-center">
                        @foreach($thread->tags as $tag)
                        <p href="" class="tag"> {{ $tag->name }} </p>
                        @endforeach
                    </div>
                    <p class="dot"></p>
                    <p>Replies: {{ $thread->replies_count }}</p>
                    <p class="dot"></p>
                    <p>Forum: <a href="{{ route('categories.show', $thread->category) }}" class="underline">
                            {{ $thread->category->title }} </a> </p>
                </div>
            </div>

        </div>
        @endforeach
    </main>
</x-layouts.forum>
