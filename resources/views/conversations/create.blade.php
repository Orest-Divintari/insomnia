<x-layouts._forum>
    @push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.2.3/trix.css">
    @endpush

    <header class="section-title">
        Start Conversation
    </header>

    <main class="section">

        <x-form._errors></x-form._errors>
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
                    <input type="text" id="participants" name="participants"
                        class="w-full bg-semi-white-mid border border-light p-1 text-md rounded focus:outline-none mt-1">
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
                <x-form._button class="text-xs" name="Start Conversation"></x-form._button>

            </form>
        </div>
    </main>
</x-layouts._forum>