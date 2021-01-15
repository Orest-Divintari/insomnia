@include('components.form._errors')
<x-layouts.search_advanced type="{{ $type }}">
    <main>
        <div class="form-container border-t-0 border-b-0">
            <form method="GET" action="{{ route('search.show') }}" class="mb-0">
                <input type="" hidden name="type" value={{ $type }}>
                <!-- ROW -->
                <div class="form-row">
                    <!-- LEFT -->
                    <div class="form-left-col ">
                        <label class="form-label" for="started_by">Keywords:</label>
                    </div>
                    <!-- RIGHT -->
                    <div class="form-right-col">
                        <p class="form-label-phone">Posted by:</p>
                        <div>
                            <input class="form-input" type="text" id="started_by" name="q" value=""
                                autocomplete="keywords">
                        </div>
                    </div>
                </div>
                <!-- ROW -->
                <div class="form-row">
                    <!-- LEFT -->
                    <div class="form-left-col "></div>
                    <!-- RIGHT -->
                    <div class="form-right-col pt-0">
                        <div class="flex flex-row-reverse items-center">
                            <label class="form-label flex-1 ml-2" for="onlyTitle">Search titles only</label>
                            <input type="checkbox" name="onlyTitle" {{ old('onlyTitle') ? 'checked' : '' }}
                                value="true">
                        </div>
                    </div>
                </div>
                <!-- ROW -->
                <div class="form-row">
                    <!-- LEFT -->
                    <div class="form-left-col ">
                        <label class="form-label" for="postedBy">Posted by:</label>
                    </div>
                    <!-- RIGHT -->
                    <div class="form-right-col">
                        <p class="form-label-phone">Posted by:</p>
                        <div>
                            <names-autocomplete name="postedBy">
                            </names-autocomplete>
                        </div>
                    </div>
                </div>
                <!-- ROW -->
                <div class="form-row">
                    <!-- LEFT -->
                    <div class="form-left-col ">
                        <label class="form-label" for="lastCreated">Last Created:</label>
                    </div>
                    <!-- RIGHT -->
                    <div class="form-right-col">
                        <p class="form-label-phone">Last Created:</p>
                        <div>
                            <input class="form-input" type="text" id="lastCreated" name="lastCreated"
                                autocomplete="lastCreated">
                        </div>
                    </div>
                </div>
                <!-- ROW -->
                <div class="form-row">
                    <!-- LEFT -->
                    <div class="form-left-col ">
                        <label class="form-label" for="numberOfReplies">Minimum number of replies:</label>
                    </div>
                    <!-- RIGHT -->
                    <div class="form-right-col">
                        <p class="form-label-phone">Last Created:</p>
                        <div>
                            <input class="form-input" type="text" id="numberOfReplies" name="numberOfReplies" value="0"
                                autocomplete="numberOfReplies">
                        </div>
                    </div>
                </div>
                <x-form._button name="Search"></x-form._button>
            </form>
        </div>
    </main>
</x-layouts.search_advanced>