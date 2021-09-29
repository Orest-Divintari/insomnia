<x-layouts.account title="Preferences" section="preferences">
    <x-slot name="main">
        <x-form.errors></x-form.errors>
        <form action="/account/preferences" method="POST" class="form-container" v-cloak>
            @method('patch')
            @csrf

            <div class="form-row">
                <!-- LEFT -->
                <div class="form-left-col">
                    <label class="form-label" for="email-options">Email options:</label>
                </div>
                <!-- RIGHT -->
                <div class="form-right-col mt-5/2">
                    <p class="form-label-phone">Email options:</p>
                    <div class="flex flex-row-reverse items-center justify-end">
                        <label for="email-on-conversation" class="text-black-semi text-sm">Receive email when a new
                            conversation message is received</label>
                        <input value="mail" class="mt-1/2 mr-2" type="checkbox" id="emai-on-conversation"
                            name="message_created[]"
                            {{ in_array('mail', $user->preferences()->message_created ) ? 'checked' : '' }}>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <!-- LEFT -->
                <div class="form-left-col">
                    <label class="form-label" for="content-options">Content options:</label>
                </div>
                <!-- RIGHT -->
                <div class="form-right-col">
                    <p class="form-label-phone">Content options:</p>
                    <watch-on-creation-checkbox :user="{{ auth()->user() }}"></watch-on-creation-checkbox>
                    <watch-on-interaction-checkbox :user="{{ auth()->user() }}"></watch-on-interaction-checkbox>
                </div>
            </div>
            <div class="flex border border-t border-l-0 border-r-0  border-white-catskill">
                <div class="w-2/6 py-3  text-right px-4 text-sm">Receive a notification when someone...</div>
            </div>
            <div class="form-row">
                <!-- LEFT -->
                <div class="form-left-col pt-4">
                    <p class="form-label" for="thread-reply-created">Replies to a watched thread:</p>
                </div>
                <!-- RIGHT -->
                <div class="form-right-col">
                    <p class="form-label-phone">Replies to a watched thread:</p>
                    <div class="flex flex-row-reverse items-center justify-end">
                        <label for="thread-reply-created" class="text-black-semi text-sm">Alert</label>
                        <input class="mt-1/2 mr-2" type="checkbox" id="thread-reply-created"
                            name="thread_reply_created[]" value="database"
                            {{ in_array('database', $user->preferences()->thread_reply_created ) ? 'checked' : '' }}>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <!-- LEFT -->
                <div class="form-left-col pt-5/2">
                    <p class="form-label" for="thread-reply-created">Likes your thread reply:</p>
                </div>
                <!-- RIGHT -->
                <div class="form-right-col pt-5/2">
                    <p class="form-label-phone">Likes your thread reply:</p>
                    <div class="flex flex-row-reverse items-center justify-end">
                        <label for="thread-reply-liked" class="text-black-semi text-sm">Alert</label>
                        <input class="mt-1/2 mr-2" type="checkbox" id="thread-reply-liked" name="thread_reply_liked[]"
                            value="database"
                            {{ in_array('database', $user->preferences()->thread_reply_liked ) ? 'checked' : '' }}>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <!-- LEFT -->
                <div class="form-left-col pt-5/2">
                    <p class="form-label" for="thread-reply-created">Posts on your profile:</p>
                </div>
                <!-- RIGHT -->
                <div class="form-right-col pt-5/2">
                    <p class="form-label-phone">Posts on your profile:</p>
                    <div class="flex flex-row-reverse items-center justify-end">
                        <label for="profile-post-created" class="text-black-semi text-sm">Alert</label>
                        <input class="mt-1/2 mr-2" type="checkbox" id="profile-post-created"
                            name="profile_post_created[]" value="database"
                            {{ in_array('database', $user->preferences()->profile_post_created ) ? 'checked' : '' }}>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <!-- LEFT -->
                <div class="form-left-col pt-5/2">
                    <p class="form-label" for="thread-reply-created">Likes your profile post:</p>
                </div>
                <!-- RIGHT -->
                <div class="form-right-col pt-5/2">
                    <p class="form-label-phone">Likes your profile post:</p>
                    <div class="flex flex-row-reverse items-center justify-end">
                        <label for="profile-post-liked" class="text-black-semi text-sm">Alert</label>
                        <input class="mt-1/2 mr-2" type="checkbox" id="profile-post-liked" name="profile_post_liked[]"
                            value="database"
                            {{ in_array('database', $user->preferences()->profile_post_liked ) ? 'checked' : '' }}>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <!-- LEFT -->
                <div class="form-left-col pt-5/2">
                    <p class="form-label" for="thread-reply-created">Comments on a post on your profile:</p>
                </div>
                <!-- RIGHT -->
                <div class="form-right-col pt-5/2">
                    <p class="form-label-phone">Comments on a post on your profile:</p>
                    <div class="flex flex-row-reverse items-center justify-end">
                        <label for="comment-on-a-post-on-your-profile_created"
                            class="text-black-semi text-sm">Alert</label>
                        <input class="mt-1/2 mr-2" type="checkbox" id="comment-on-a-post-post-on-your-profile-created"
                            name="comment_on_a_post_on_your_profile_created[]" value="database"
                            {{ in_array('database', $user->preferences()->comment_on_a_post_on_your_profile_created ) ? 'checked' : '' }}>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <!-- LEFT -->
                <div class="form-left-col pt-5/2">
                    <p class="form-label" for="thread-reply-created">Comments on your status:</p>
                </div>
                <!-- RIGHT -->
                <div class="form-right-col pt-5/2">
                    <p class="form-label-phone">Comments your status:</p>
                    <div class="flex flex-row-reverse items-center justify-end">
                        <label for="comment-on-your-post-on-your-profile" class="text-black-semi text-sm">Alert</label>
                        <input class="mt-1/2 mr-2" type="checkbox" id="comment-on-your-post-on-your-profile"
                            name="comment_on_your_post_on_your_profile_created[]" value="database"
                            {{ in_array('database', $user->preferences()->comment_on_your_post_on_your_profile_created ) ? 'checked' : '' }}>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <!-- LEFT -->
                <div class="form-left-col pt-5/2">
                    <p class="form-label" for="thread-reply-created">Comments on your post on other profile:</p>
                </div>
                <!-- RIGHT -->
                <div class="form-right-col pt-5/2">
                    <p class="form-label-phone">Comments your post on other profile:</p>
                    <div class="flex flex-row-reverse items-center justify-end">
                        <label for="comment-on-your-profile-post-created" class="text-black-semi text-sm">Alert</label>
                        <input class="mt-1/2 mr-2" type="checkbox" id="comment-on-your-profile-post-created"
                            name="comment_on_your_profile_post_created[]" value="database"
                            {{ in_array('database', $user->preferences()->comment_on_your_profile_post_created ) ? 'checked' : '' }}>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <!-- LEFT -->
                <div class="form-left-col pt-5/2">
                    <p class="form-label" for="thread-reply-created">Comments on participated profile post:</p>
                </div>
                <!-- RIGHT -->
                <div class="form-right-col pt-5/2">
                    <p class="form-label-phone">Comments on participated profile post:</p>
                    <div class="flex flex-row-reverse items-center justify-end">
                        <label for="comment-on-your-profile-post-created" class="text-black-semi text-sm">Alert</label>
                        <input class="mt-1/2 mr-2" type="checkbox" id="comment-on-participated-profile-post-created"
                            name="comment_on_participated_profile_post_created[]" value="database"
                            {{ in_array('database', $user->preferences()->comment_on_participated_profile_post_created ) ? 'checked' : '' }}>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <!-- LEFT -->
                <div class="form-left-col pt-5/2">
                    <p class="form-label" for="thread-reply-created">Likes your profile post comment:</p>
                </div>
                <!-- RIGHT -->
                <div class="form-right-col pt-5/2">
                    <p class="form-label-phone">Likes your profile post comment:</p>
                    <div class="flex flex-row-reverse items-center justify-end">
                        <label for="comment-liked" class="text-black-semi text-sm">Alert</label>
                        <input class="mt-1/2 mr-2" type="checkbox" id="comment-liekd" name="comment_liked[]"
                            value="database"
                            {{ in_array('database', $user->preferences()->comment_liked ) ? 'checked' : '' }}>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <!-- LEFT -->
                <div class="form-left-col pt-5/2">
                    <p class="form-label" for="thread-reply-created">Likes your conversation message:</p>
                </div>
                <!-- RIGHT -->
                <div class="form-right-col pt-5/2">
                    <p class="form-label-phone">Likes your conversation message:</p>
                    <div class="flex flex-row-reverse items-center justify-end">
                        <label for="message-liked" class="text-black-semi text-sm">Alert</label>
                        <input class="mt-1/2 mr-2" type="checkbox" id="message-liked" name="message_liked[]"
                            value="database"
                            {{ in_array('database', $user->preferences()->message_liked ) ? 'checked' : '' }}>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <!-- LEFT -->
                <div class="form-left-col pt-5/2">
                    <p class="form-label" for="thread-reply-created">Starts following you:</p>
                </div>
                <!-- RIGHT -->
                <div class="form-right-col pt-5/2">
                    <p class="form-label-phone">Starts following you:</p>
                    <div class="flex flex-row-reverse items-center justify-end">
                        <label for="user-followed-you" class="text-black-semi text-sm">Alert</label>
                        <input class="mt-1/2 mr-2" type="checkbox" id="user-followed-you" name="user_followed_you[]"
                            value="database"
                            {{ in_array('database', $user->preferences()->user_followed_you ) ? 'checked' : '' }}>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <!-- LEFT -->
                <div class="form-left-col pt-5/2">
                    <p class="form-label" for="mentioned-in-thread">Mentions you in a thread:</p>
                </div>
                <!-- RIGHT -->
                <div class="form-right-col pt-5/2">
                    <p class="form-label-phone">Mentions you in a thread:</p>
                    <div class="flex flex-row-reverse items-center justify-end">
                        <label for="mentioned-in-thread" class="text-black-semi text-sm">Alert</label>
                        <input class="mt-1/2 mr-2" type="checkbox" id="mentioned-in-thread" name="mentioned_in_thread[]"
                            value="database"
                            {{ in_array('database', $user->preferences()->mentioned_in_thread ) ? 'checked' : '' }}>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <!-- LEFT -->
                <div class="form-left-col pt-5/2">
                    <p class="form-label" for="mentioned-in-thread-reply">Mentions you in a thread reply:</p>
                </div>
                <!-- RIGHT -->
                <div class="form-right-col pt-5/2">
                    <p class="form-label-phone">Mentions you in a thread reply:</p>
                    <div class="flex flex-row-reverse items-center justify-end">
                        <label for="mentioned-in-thread-reply" class="text-black-semi text-sm">Alert</label>
                        <input class="mt-1/2 mr-2" type="checkbox" id="mentioned-in-thread-reply"
                            name="mentioned_in_thread_reply[]" value="database"
                            {{ in_array('database', $user->preferences()->mentioned_in_thread_reply ) ? 'checked' : '' }}>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <!-- LEFT -->
                <div class="form-left-col pt-5/2">
                    <p class="form-label" for="mentioned-in-profile-post">Mentions you in a profile post:</p>
                </div>
                <!-- RIGHT -->
                <div class="form-right-col pt-5/2">
                    <p class="form-label-phone">Mentions you in a profile post:</p>
                    <div class="flex flex-row-reverse items-center justify-end">
                        <label for="mentioned-in-profile-post" class="text-black-semi text-sm">Alert</label>
                        <input class="mt-1/2 mr-2" type="checkbox" id="mentioned-in-profile-post"
                            name="mentioned_in_profile_post[]" value="database"
                            {{ in_array('database', $user->preferences()->mentioned_in_profile_post ) ? 'checked' : '' }}>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <!-- LEFT -->
                <div class="form-left-col pt-5/2">
                    <p class="form-label" for="mentioned-in-comment">Mentions you in a comment:</p>
                </div>
                <!-- RIGHT -->
                <div class="form-right-col pt-5/2">
                    <p class="form-label-phone">Mentions you in a comment:</p>
                    <div class="flex flex-row-reverse items-center justify-end">
                        <label for="mentioned-in-comment" class="text-black-semi text-sm">Alert</label>
                        <input class="mt-1/2 mr-2" type="checkbox" id="mentioned-in-comment"
                            name="mentioned_in_comment[]" value="database"
                            {{ in_array('database', $user->preferences()->mentioned_in_comment ) ? 'checked' : '' }}>
                    </div>
                </div>
            </div>

            <x-form.button class="text-xs" name="Save"></x-form.button>
        </form>
    </x-slot>
</x-layouts.account>