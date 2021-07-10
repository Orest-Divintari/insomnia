<x-layouts.forum>
    <main class="section">
        <div class="flex ">
            <div class="h-full w-1/5 border border-gray-lighter border-t-8 text-gray-shuttle">
                <h1 class="p-5/2 text-md border-b border-gray-lighter"> Dashboard </h1>
                <a class="block py-2 px-4 {{ $section == 'group-categories' ? 'text-blue-mid border-l-2 border-blue-mid font-bold bg-white-smoke' : 'hover:bg-white-smoke hover:text-black-semi' }}"
                    href="{{ route('admin.group-categories.index') }}">Group categories</a>

                <a class="block py-2 px-4 {{ $section == 'categories' ? 'text-blue-mid border-l-2 border-blue-mid font-bold bg-white-smoke' : 'hover:bg-white-smoke hover:text-black-semi' }}"
                    href="{{ route('admin.categories.index') }}">Categories</a>
            </div>
            <div class="ml-5 w-full">
                {{ $slot }}
            </div>
        </div>
    </main>
</x-layouts.forum>