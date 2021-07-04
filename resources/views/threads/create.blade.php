<x-layouts.forum>
    @push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.2.3/trix.css">
    @endpush

    <header class="section-title">
        Post Thread
    </header>

    <main class="section">

        <x-form.errors></x-form.errors>

        <div class="form-container">
            <form action="{{ route('threads.create', $category) }}" method="POST">
                @csrf
                <input type="hidden" name="category_id" value="{{ $category->id }}">
                <div class="p-4">
                    <input type="text" name="title"
                        class="w-full bg-semi-white-mid border border-light p-1 text-xl rounded focus:outline-none "
                        placeholder="Thread title" value="{{ old('title') }} " required>
                </div>
                <wysiwyg value="{{ old('body') }}" name="body" class="p-4" :style-attributes="'min-h-64'" required>
                </wysiwyg>

                <div class="border-t border-white-catskill">
                    <div class="form-row">
                        <!-- LEFT -->
                        <div class="form-left-col ">
                            <label class="form-label" for="tags">Tags</label>
                        </div>
                        <!-- RIGHT -->
                        <div class="form-right-col">
                            <p class="form-label-phone">Tags</p>
                            <div>
                                <input class="form-input bg-semi-white-mid" type="text" id="tags" name="tags"
                                    autocomplete="tags">
                                <p class="form-input-description">Multiple tags may be separated by comma</p>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <!-- LEFT -->
                        <div class="form-left-col">
                            <label class="form-label" for="tags">Options</label>
                        </div>
                        <!-- RIGHT -->
                        <div class="flex flex-col form-right-col">
                            <p class="form-label-phone">Options</p>
                            <div class="flex items-center">

                                <input class="form-input bg-semi-white-mid w-3 h-3" type="checkbox" id="watch_thread"
                                    name="watch_thread" autocomplete="watch_thread">
                                <p class=" ml-2 form-input-description p-0">Watch this thread...</p>
                            </div>
                            <div class="flex items-center">
                                <input class="form-input bg-semi-white-mid w-3 h-3" type="checkbox"
                                    id="receive_email_notifications" name="receive_email_notifications"
                                    autocomplete="receive_email_notifications">
                                <p class=" ml-2 form-input-description p-0">Receive email notifications</p>
                            </div>
                        </div>
                    </div>
                </div>
                <x-form.button class="text-xs" name="Post Thread"></x-form.button>

            </form>
        </div>
    </main>
</x-layouts.forum>