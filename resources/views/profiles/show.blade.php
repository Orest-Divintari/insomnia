<x-layouts.forum>
    <profile :posts="{{ $profilePosts->toJson() }}" :profile-owner="{{ $user }}"></profile>
</x-layouts.forum>