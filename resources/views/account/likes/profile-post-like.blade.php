<div class="pl-5/2">
    <div class="text-md flex items-center">
        <profile-popover :user="{{ $like->liker }}" trigger-classes="blue-link text-md mr-3/2"></profile-popover>
        liked <a href="{{ route('profile-posts.show', $like->likeable) }}" class="blue-link mx-3/2">your post</a>
        on {{ $like->likeable->profileOwner->name }}'s profile
    </div>
    <html-to-text text-classes="text-black-semi text-smaller" value="{{ $like->likeable->body }}"></html-to-text>
    <p class="text-gray-shuttle text-smaller"> {{ $like->likeable->date_created }} </p>
</div>