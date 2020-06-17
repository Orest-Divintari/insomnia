<x-layouts._forums>
    <header class="flex justify-between">
        <div>
            <h1 class="section-title">
                {{$category->title}}
            </h1>
            <p class="text-smaller text-gray-lightest">{{ $category->excerpt }}</p>
        </div>
        @auth
        <a href="{{route('threads.create')}}">Post Thread</a>
        @endauth
    </header>
    <main class="section">

    </main>

    @foreach($category->threads as $thread)
    <p>{{$thread->title}}</p>
    <a href="/threads/{{$thread->slug}}"> {{ $thread->title }} </a>
    @endforeach
</x-layouts._forums>