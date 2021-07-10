<x-layouts.admin-dashboard section="categories">
    <header class="section-title">
        New Category
    </header>

    <main class="section w-full">
        <x-form.errors></x-form.errors>

        <div class="form-container">
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
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
                                    value="{{ old('title') }}" required>
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
                                    value="{{ old('excerpt') }}" required>
                                <p class="form-input-description">Enter a short description for the group category</p>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <!-- LEFT -->
                        <div class="form-left-col ">
                            <label class="form-label" for="parent-id">Parent</label>
                        </div>
                        <!-- RIGHT -->
                        <div class="form-right-col ">
                            <p class="form-label-phone">Parent</p>
                            <select id="parent-id"
                                class="focus:outline-none border border-gray-lighter rounded p-2 w-full"
                                name="parent_id">
                                <option value="">None</option>
                                @foreach($allCategories as $parentCategory)
                                <option value="{{ $parentCategory->id }}">{{ $parentCategory->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <!-- LEFT -->
                        <div class="form-left-col ">
                            <label class="form-label" for="group-id">Group</label>
                        </div>
                        <!-- RIGHT -->
                        <div class="form-right-col">
                            <p class="form-label-phone">Group</p>
                            <div>
                                <select id="group-id"
                                    class="focus:outline-none border border-gray-lighter rounded p-2 w-full"
                                    name="group_category_id">
                                    <option value="">None</option>
                                    @foreach($allGroupCategories as $groupCategory)
                                    <option value="{{ $groupCategory->id }}">{{ $groupCategory->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <!-- LEFT -->
                        <div class="form-left-col ">
                            <label class="form-label" for="image">Image</label>
                        </div>
                        <!-- RIGHT -->
                        <div class="form-right-col">
                            <p class="form-label-phone">Image</p>
                            <div>
                                <input id="image=" name="image_path" class="p-2" type="file" class="text-xs"
                                    accept="image/*" />
                            </div>
                        </div>
                    </div>


                </div>
                <x-form.button class="text-xs" name="Create"></x-form.button>
            </form>
        </div>
    </main>

</x-layouts.admin-dashboard>