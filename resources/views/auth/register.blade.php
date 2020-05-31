<x-layouts._forums>

    <div class="p-6">
        <h1 class="text-4xl font-bold tracking-wide my-5">Register</h1>

        <form action="{{route('register')}}" method="POST">
            @csrf
            <div
                class="w-full py-4 bg-red-alert rounded mb-3 shadow-md font-light border-l-2 border-red-alert-text text-red-alert-text pl-5 flex items-center">

                <span class="fas fa-ban mr-3"></span>
                <p>invalid password</p>
            </div>
            <!-- @if($errors->any)
            @foreach($errors->all() as $error)
            <div class="w-full py-4 bg-red-alert">
                {{$error}}
            </div>
            @endforeach
            @endif -->
            <div class=" border border-blue-form-border rounded flex flex-col flex-wrap">
                <!-- ROW -->
                <div class="flex items-stretch">
                    <!-- LEFT -->
                    <div
                        class="hidden smaller:flex items-center pt-4 w-2/6 bg-blue-form-side border-r border-blue-form-border px-6">
                        <div class="flex-grow text-right">
                            <label class="text-gray-800 text-sm  tracking-wide" for="email">Username:</label>
                            <div class="leading-none text-xs text-gray-form-label">Required</div>
                        </div>
                    </div>
                    <!-- RIGHT -->
                    <div class="flex flex-col justify-center w-full smaller:w-4/6 pt-4 px-6">
                        <label class="text-gray-800 text-sm  tracking-wide smaller:hidden mb-2"
                            for="email">Username</label>
                        <input id="email"
                            class=" bg-blue-form-input border border-blue-form-border rounded py-1 px-3  w-full focus:outline-none focus:bg-white"
                            type="text" required>
                    </div>
                </div>
                <!-- ROW -->
                <div class="flex items-stretch">
                    <!-- LEFT -->
                    <div
                        class="hidden  smaller:flex items-center pt-8 w-2/6 bg-blue-form-side border-r border-blue-form-border px-6 ">
                        <div class="flex-grow text-right">
                            <label class="text-gray-800 text-sm  tracking-wide" for="email">Email:</label>
                            <div class="leading-none text-xs text-gray-form-label">Required</div>
                        </div>
                    </div>
                    <!-- RIGHT -->
                    <div class="flex flex-col justify-center w-full smaller:w-4/6 pt-4 pt-8 px-6">
                        <label class="text-gray-800 text-sm  tracking-wide smaller:hidden mb-2"
                            for="email">Email</label>
                        <input id="email"
                            class=" bg-blue-form-input border border-blue-form-border rounded py-1 px-3  w-full focus:outline-none focus:bg-white"
                            type="text" required>
                    </div>
                </div>
                <!-- ROW -->
                <div class="flex items-stretch">
                    <!-- LEFT -->
                    <div
                        class="hidden smaller:flex pt-8 w-2/6 bg-blue-form-side border-r border-blue-form-border px-6 text-right">
                        <div class="flex-grow text-right">
                            <label class="text-gray-800 text-sm  tracking-wide" for="email">Password:</label>
                            <div class="leading-none text-xs text-gray-form-label">Required</div>
                        </div>
                    </div>
                    <!-- RIGHT -->
                    <div class="flex flex-col justify-center w-full smaller:w-4/6 px-6 pt-8">
                        <label class="text-gray-800 text-sm  tracking-wide smaller:hidden mb-2"
                            for="password">Password</label>
                        <input id="password"
                            class=" bg-blue-form-input border border-blue-form-border rounded py-1 px-3  w-full focus:outline-none focus:bg-white"
                            type="password" required>
                    </div>
                </div>
                <!-- ROW -->
                <div class="flex items-stretch">
                    <!-- LEFT -->
                    <div
                        class="hidden smaller:flex pt-5 w-2/6 bg-blue-form-side border-r border-blue-form-border px-6 text-right">
                        <div class="flex-grow text-right self-end leading-none">
                            <label class="text-gray-800 text-sm tracking-wide" for="email">Confirm Password:</label>
                            <div class="leading-none text-xs text-gray-form-label">Required</div>
                        </div>
                    </div>
                    <!-- RIGHT -->
                    <div class="flex flex-col justify-end w-full smaller:w-4/6 px-6 pt-5">
                        <label class="text-gray-800 text-sm  tracking-wide smaller:hidden mb-2" for="password">Confirm
                            Password</label>
                        <input id="password"
                            class=" bg-blue-form-input border border-blue-form-border rounded py-1 px-3  w-full focus:outline-none focus:bg-white"
                            type="password" required>
                    </div>
                </div>
                <!-- ROW -->
                <div class="flex items-stretch">
                    <!-- LEFT -->
                    <div
                        class="hidden  smaller:flex items-center  pt-8 pb-4 w-2/6 bg-blue-form-side border-r border-blue-form-border px-6 ">
                        <div class="flex-grow text-right">
                            <label class="text-gray-800 text-sm  tracking-wide" for="email">Verification:</label>
                            <div class="leading-none text-xs text-gray-form-label">Required</div>
                        </div>
                    </div>
                    <!-- RIGHT -->
                    <div class="flex flex-col justify-center w-full smaller:w-4/6 pt-8 pb-4 px-6">
                        <label class="text-gray-800 text-sm  tracking-wide smaller:hidden mb-2"
                            for="email">Verification</label>
                        <input id="email"
                            class=" bg-blue-form-input border border-blue-form-border rounded py-1 px-3  w-full focus:outline-none focus:bg-white"
                            type="text" required>
                    </div>
                </div>
                <!-- BUTTON -->
                <div class="bg-blue-form-bottom smaller:flex smaller:py-3">
                    <div class="smaller:w-2/6"></div>
                    <div
                        class="w-full smaller:w-4/6 py-3 smaller:py-0 smaller:pl-6 bg-blue-form-bottom flex items-center justify-center smaller:justify-start">
                        <button
                            class="block py-2 px-12 font-bold bg-blue-form-button text-white rounded hover:bg-blue-form-hover-button"
                            type="submit">Register</button>
                    </div>

                </div>
            </div>



        </form>
    </div>
</x-layouts._forums>