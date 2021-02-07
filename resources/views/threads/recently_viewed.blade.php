<x-layouts._forum>

    <header class="section-title">History</header>
    <main class="section">
        <p class="text-black-semi text-md">Your most recently viewed threads</p>
        <table class="table-fixed mt-4">
            <thead class="bg-white-catskill">
                <tr class="text-smaller text-left">
                    <th class="py-2 px-4">Title</th>
                    <th class="py-2 px-4">Forum</th>
                    <th class="py-2 px-4">Started by</th>
                    <th class="py-2 px-4">Last post by</th>
                    <th class="py-2 px-4">Replies</th>
                    <th class="border py-2 px-4">Thread read date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($threads as $thread)
                <tr class="border text-sm text-left text-black-semi">
                    <td class="p-4 w-180  {{ $thread->has_been_updated ? 'font-bold' : '' }}">
                        <a class="blue-link" href="{{ route('threads.show', $thread) }}"> {{ $thread->title }}</a>
                    </td>
                    <td class="p-4 w-64 {{ $thread->has_been_updated ? 'font-bold' : '' }}">
                        <a class="blue-link" href="{{ route('categories.show', $thread->category) }}">
                            {{ $thread->category->title }}</a>
                    </td>
                    <td class="p-4 w-48">
                        {{ $thread->poster->name }}
                    </td>
                    <td class="p-4 w-48">
                        {{ $thread->recentReply->poster->name}}
                    </td>
                    <td class="p-4 w-42">
                        {{ $thread->replies_count }}
                    </td>
                    <td class="p-4 w-56">
                        {{ $thread->read_at }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</x-layouts._forum>