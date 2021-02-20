<x-layouts.forum>
    @push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.2.3/trix.css">
    @endpush

    <header class="section-title">
        Start Conversation
    </header>

    <main class="section">

        <x-form.errors></x-form.errors>
        <x-breadcrumb.container>
            <x-breadcrumb.item :title="'Forum'" :route="route('forum')"></x-breadcrumb.item>
            <x-breadcrumb.leaf :title="'Conversations'" :route="route('conversations.index')">
            </x-breadcrumb.leaf>
        </x-breadcrumb.container>
        <div class="form-container">
            <form action="/conversations" method="POST">
                @csrf
                <div class="p-4">
                    <label for="participants" class="text-black-semi text-smaller">Participants:</label>
                    <names-autocomplete
                        styleClasses="w-full bg-semi-white-mid border border-light p-1 text-md rounded focus:outline-none mt-1"
                        initial-participant="{{ $participant }}" name="participants">
                    </names-autocomplete>

                    <p class="text-xs text-gray-lightest mt-2">You may enter multiple names separated by comma.</p>
                </div>
                <div class="p-4">
                    <input type="text" name="title"
                        class="w-full bg-semi-white-mid border border-light p-1 text-xl rounded focus:outline-none "
                        placeholder="Title..." value="{{ old('title') }}" required>
                </div>
                <wysiwyg value="{{ old('message') }}" name="message" class="p-4" :style-attributes="'min-h-64'"
                    required>
                </wysiwyg>
                <div class="border-t border-white-catskill">
                    <div class="form-row">
                        <!-- LEFT -->
                        <div class="form-left-col ">

                        </div>
                        <!-- RIGHT -->
                        <div class="form-right-col">

                            <div class="flex items-center">
                                <input class="form-input mt-1/2 bg-semi-white-mid w-3 h-3" type="checkbox" id="admin"
                                    name="admin">
                                <p class="ml-2 form-input-description p-0 text-sm text-black-form-text">Allow anyone
                                    in
                                    the conversation to
                                    invite
                                    others.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <x-form.button class="text-xs" name="Start Conversation"></x-form.button>

            </form>
        </div>
    </main>
</x-layouts.forum>
