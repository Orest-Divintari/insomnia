<x-layouts._forum>
    @push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.2.3/trix.css">
    @endpush
    <conversation :conversation="{{ $conversation }}" inline-template v-cloak>
        <div>
            <header>
                <h1 class="font-bold text-3xl mb-1" v-text="title"></h1>
                <div class="flex items-center text-smaller text-gray-lightest">
                    <span class="fas fa-user mr-1"></span>
                    @foreach($participants as $participant)
                    <p>
                        {{ $participant->name }}
                    </p>
                    @if(!$loop->last)
                    <p class="mr-1">, </p>
                    @endif
                    @endforeach

                    <p class=" dot"></p>
                    <span class="mr-1 fas fa-clock"></span>
                    <a href="{{route('conversations.show', $conversation->slug)}}" class="mr-1 hover:underline">
                        {{ $conversation->date_created }}</a>
                </div>
            </header>

            <main class="section">
                <x-breadcrumb.container>
                    <x-breadcrumb.item :title="'Forum'" :route="route('forum')">
                    </x-breadcrumb.item>
                    <x-breadcrumb.leaf :title="'Conversations'" :route="route('conversations.index')">
                    </x-breadcrumb.leaf>
                </x-breadcrumb.container>

                <div class="mt-7 flex justify-end">
                    <button class="btn-white-blue mr-1">Edit</button>
                    <button class="btn-white-blue mr-1">Star</button>
                    <button class="btn-white-blue mr-1">Mark read</button>
                    <button class="btn-white-blue mr-1">Leave</button>
                </div>



                <replies :repliable="conversation" :replies="{{ json_encode($messages) }}"></replies>
            </main>
        </div>
    </conversation>

</x-layouts._forum>