<x-form.errors></x-form.errors>
<x-layouts.advanced-search type="{{ $type }}">
    <main>
        <div class="form-container border-t-0 border-b-0">
            <form method="GET" action="{{ route('search.index') }}" class="mb-0">
                <input type="" hidden name="type" value={{ $type }}>
                <!-- ROW -->
                <div class="form-row">
                    <!-- LEFT -->
                    <div class="form-left-col ">
                        <label class="form-label" for="started_by">Keywords:</label>
                    </div>
                    <!-- RIGHT -->
                    <div class="form-right-col">
                        <p class="form-label-phone">Keywords:</p>
                        <div>
                            <input class="form-input" type="text" id="started_by" name="q" value=""
                                autocomplete="keywords">
                        </div>
                    </div>
                </div>

                <!-- ROW -->
                <div class="form-row">
                    <!-- LEFT -->
                    <div class="form-left-col ">
                        <label class="form-label" for="posted_by">Posted by:</label>
                    </div>
                    <!-- RIGHT -->
                    <div class="form-right-col">
                        <p class="form-label-phone">Posted by:</p>
                        <div>
                            <names-autocomplete input-name="posted_by">
                            </names-autocomplete>
                        </div>
                    </div>
                </div>
                <!-- ROW -->
                <div class="form-row">
                    <!-- LEFT -->
                    <div class="form-left-col ">
                        <label class="form-label" for="profile_owner">Posted on the profile of member: </label>
                    </div>
                    <!-- RIGHT -->
                    <div class="form-right-col">
                        <p class="form-label-phone">Posted on the profile of member: </p>
                        <div>
                            <names-autocomplete input-name="profile_owner">
                            </names-autocomplete>
                        </div>
                    </div>
                </div>
                <!-- ROW -->
                <div class="form-row">
                    <!-- LEFT -->
                    <div class="form-left-col ">
                        <label class="form-label" for="last_created">Last Created:</label>
                    </div>
                    <!-- RIGHT -->
                    <div class="form-right-col">
                        <p class="form-label-phone">Last Created:</p>
                        <div>
                            <input class="form-input" type="text" id="last_created" name="last_created"
                                autocomplete="last_created">
                        </div>
                    </div>
                </div>
                <x-form.button name="Search"></x-form.button>
            </form>
        </div>
    </main>
</x-layouts.advanced-search>