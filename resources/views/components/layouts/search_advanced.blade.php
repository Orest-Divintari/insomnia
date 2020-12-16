<x-layouts._forum>
    <div class="section">
        <h1 class="section-title">Search</h1>
    </div>
    <div class="border border-gray-lighter rounded  mt-5">
        <div class="flex">
            <a href="/search/advanced"
                class="w-42 text-smaller  py-5/2 px-5 rounded  rounded-b-none {{ $type == '' ? 'active' : 'not-active' }}">
                Search everything
            </a>
            <a href="/search/advanced?type=thread"
                class="w-42 text-smaller  py-5/2 px-5  rounded-b-none {{ $type == 'thread' ? 'active' : 'not-active' }} rounded ">
                Search threads
            </a>
            <a href="/search/advanced?type=profile_post"
                class="w-42 text-smaller  py-5/2 px-5 rounded-b-none {{ $type == 'profile_post' ? 'active' : 'not-active' }} rounded ">
                Search profile posts
            </a>
            <a href="/search/advanced?type=tag"
                class="w-42 text-smaller  py-5/2 px-5  rounded-b-none {{ $type == 'tag' ? 'active' : 'not-active' }} rounded ">
                Search tags
            </a>
        </div>
        <div class="bg-gray-lighter py-1/2"></div>
        {{ $slot }}

    </div>
</x-layouts._forum>