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
                        <label class="form-label" for="tags">Tags:</label>
                    </div>
                    <!-- RIGHT -->
                    <div class="form-right-col">
                        <p class="form-label-phone">Tags:</p>
                        <div>
                            <input class="form-input" type="text" id="tags" name="q" value="" autocomplete="tags">
                        </div>
                    </div>
                </div>

                <x-form._button name="Search"></x-form._button>
            </form>
        </div>
    </main>
</x-layouts.search_advanced>