<x-layouts.forum>
    <header>
        <h1 class="section-title">Profile posts</h1>
    </header>
    <main class="section">
        <profile-posts :profile-post-filters="{{ json_encode($profilePostFilters) }}"
            :posts="{{ json_encode($profilePosts) }}" :show-receiver="true"></profile-posts>

    </main>
</x-layouts.forum>