<x-layouts.forum>
    <header>
        <h1 class="section-title">Log in</h1>
    </header>
    <main class="section">
        <x-form.errors></x-form.errors>
        <div class="form-container">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <!-- ROW -->
                <div class="form-row">
                    <!-- LEFT -->
                    <div class="form-left-col ">
                        <label class="form-label" for="username-or-password">Your name or email address:</label>
                    </div>
                    <!-- RIGHT -->
                    <div class="form-right-col">
                        <p class="form-label-phone">Your name or email address:</p>
                        <div>
                            <input class="form-input" type="email" id="username-or-password" name="email" required
                                autocomplete="email">
                        </div>
                    </div>
                </div>
                <!-- ROW -->
                <div class="form-row">
                    <!-- LEFT -->
                    <div class="form-left-col">
                        <label class="form-label" for="password">Password:</label>
                    </div>
                    <!-- RIGHT -->
                    <div class="form-right-col">
                        <p class="form-label-phone">Password:</p>
                        <div>
                            <input class="form-input" type="password" id="password" name="password" required
                                autocomplete="current-password">
                            <a class="block text-sm text-blue-form-link hover:underline" href="">Forgot your
                                password?</a>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-left-col "></div>
                    <div class="form-right-col">
                        <div class="flex flex-row-reverse items-center">
                            <label class="form-label flex-1 ml-2" for="stay-logged-in">Stay logged in</label>
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
                <x-form.button name="Log in"></x-form.button>
            </form>
        </div>
    </main>

</x-layouts.forum>
