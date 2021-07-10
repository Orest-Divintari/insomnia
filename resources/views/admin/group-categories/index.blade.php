<x-layouts.admin-dashboard section="group-categories">
    <header class="section-title flex items-center justify-between">
        <h1> Group categories</h1>

        <a href="{{ route('admin.group-categories.create') }}" class="btn-post"><span
                class="fas fa-pen text-white text-xs mr-1"></span>New</a>
    </header>

    <main>
        @if(empty($groupCategories->items()))
        <p class="
        border border-gray-lighter
        p-4
        rounded
        mb-2
        text-black-semi text-sm
      ">There are no group categories to display.</p>
        @else
        <table class="table-fixed mt-4">
            <thead class="bg-white-catskill">
                <tr class="text-smaller text-left">
                    <th class="py-2 px-4">Title</th>
                    <th class="py-2 px-4">Excerpt</th>
                    <th class="py-2 px-4">Categories</th>
                    <th class="py-2 px-4">Threads</th>
                    <th class="py-2 px-4">Replies</th>
                    <th class="py-2 px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groupCategories as $groupCategory)
                <tr class="border text-sm text-left text-black-semi">
                    <td class="p-4 w-64 ">
                        {{ $groupCategory->title }}
                    </td>
                    <td class="p-4">
                        {{ $groupCategory->excerpt }}
                    </td>
                    <td class="p-4 w-48">
                        {{ $groupCategory->categories_count }}
                    </td>
                    <td class="p-4 w-48">
                        {{ $groupCategory->threads_count }}
                    </td>
                    <td class="p-4 w-42">
                        {{ $groupCategory->replies_count }}
                    </td>
                    <td class="p-4 w-42">
                        <a href="{{ route('admin.group-categories.edit', $groupCategory) }}"
                            class="btn-white-blue">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <paginator :dataset="{{ json_encode($groupCategories) }}"></paginator>
        @endif
    </main>
</x-layouts.admin-dashboard>