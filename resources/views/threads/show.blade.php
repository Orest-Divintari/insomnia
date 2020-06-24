<x-layouts._forum>

    <thread :thread="{{ $thread }}" inline-template>
        <div>
            <header>
                <h1 class="font-bold text-3xl mb-1"> {{$thread->title}} </h1>
                <div class="flex items-center text-smaller text-gray-lightest">
                    <span class="fas fa-user mr-1"></span>
                    <a href="" class="hover:underline">
                        {{ $thread->poster->shortName }}</a>
                    <p class=" dot"></p>
                    <span class="mr-1 fas fa-clock"></span>
                    <a href="{{route('threads.show', $thread->slug)}}" class="mr-1 hover:underline">
                        {{ $thread->date_created }}</a>
                    <p class=" dot"></p>
                    <i class="fas fa-long-arrow-alt-down mr-1 "></i>
                    <p class="hover:underline">Sort</p>
                </div>
            </header>

            <main class="section">
                <x-breadcrumb.container>
                    <x-breadcrumb.item :title="'Forum'" :route="route('forum')"></x-breadcrumb.item>
                    <x-breadcrumb.item :title="$thread->category->group->title" :route="route('forum')">
                    </x-breadcrumb.item>
                    <x-breadcrumb.item :title="$thread->category->category->title"
                        :route="route('categories.show', $thread->category->category->slug)">
                    </x-breadcrumb.item>
                    <x-breadcrumb.leaf :title="$thread->category->title"
                        :route="route('categories.show', $thread->category->slug)">
                    </x-breadcrumb.leaf>
                </x-breadcrumb.container>

                <div class="mt-7 flex justify-end">
                    <button class="btn-thread-control mr-1">Ignore</button>
                    <button class="btn-thread-control mr-1">Watch</button>
                    <button class="btn-thread-control mr-1">Lock</button>
                    <button class="btn-thread-control mr-1">Pin</button>
                </div>

                <replies :thread="thread"></replies>
            </main>
        </div>
    </thread>

</x-layouts._forum>