<x-layouts.admin-dashboard section="group-categories">

    <header class="section-title">
        Edit Group Category
    </header>

    <main class="section w-full">
        <x-form.errors></x-form.errors>

        <div class="form-container">
            <form action="{{ route('admin.group-categories.update', $groupCategory) }}" method="POST">
                @csrf
                @method('patch')
                <div class="border-t border-white-catskill">
                    <div class="form-row">
                        <!-- LEFT -->
                        <div class="form-left-col ">
                            <label class="form-label" for="title">Title</label>
                        </div>
                        <!-- RIGHT -->
                        <div class="form-right-col">
                            <p class="form-label-phone">Title</p>
                            <div>
                                <input class="form-input bg-semi-white-mid" type="text" id="title" name="title"
                                    value="{{ $groupCategory->title }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <!-- LEFT -->
                        <div class="form-left-col ">
                            <label class="form-label" for="excerpt">Excerpt</label>
                        </div>
                        <!-- RIGHT -->
                        <div class="form-right-col">
                            <p class="form-label-phone">Excerpt</p>
                            <div>
                                <input class="form-input bg-semi-white-mid" type="text" id="excerpt" name="excerpt"
                                    value="{{ $groupCategory->excerpt }}" required>
                                <p class="form-input-description">Enter a short description for the group category</p>
                            </div>
                        </div>
                    </div>
                </div>
                <x-form.button class="text-xs" name="Save"></x-form.button>
            </form>
        </div>
    </main>
</x-layouts.admin-dashboard>