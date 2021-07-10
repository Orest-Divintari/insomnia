<x-layouts.admin-dashboard section="categories">

    <header class="section-title">
        Edit Category
    </header>

    <main class="section w-full">
        <x-form.errors></x-form.errors>

        <div class="form-container">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST">
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
                                    value="{{ $category->title }}" required>
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
                                    value="{{ $category->excerpt }}" required>
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
                                @if($category->hasParent())
                                <option value="{{ $category->category->id }}">{{ $category->category->title }}</option>
                                @else
                                <option value=""> None </option>
                                @endif
                                @foreach($allCategories as $parentCategory)
                                <option value="{{ $parentCategory->id }}">{{ $parentCategory->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <!-- LEFT -->
                        <div class="form-left-col ">
                            <label class="form-label" for="parent-id">Group</label>
                        </div>
                        <!-- RIGHT -->
                        <div class="form-right-col ">
                            <p class="form-label-phone">Group</p>
                            <select id="group-id"
                                class="focus:outline-none border border-gray-lighter rounded p-2 w-full"
                                name="group_category_id">
                                <option value="{{ $category->group->id }}">{{ $category->group->title }}</option>
                                @foreach($allGroupCategories as $groupCategory)
                                <option value="{{ $groupCategory->id }}">{{ $groupCategory->title }}</option>
                                @endforeach
                            </select>
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
                <x-form.button class="text-xs" name="Save"></x-form.button>
            </form>
        </div>
    </main>
</x-layouts.admin-dashboard>