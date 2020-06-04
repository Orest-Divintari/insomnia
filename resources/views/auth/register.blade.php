<x-layouts._forums>
    @push('scripts')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endpush
    <header>
        <h1 class="section-title">Register</h1>
    </header>
    <main class="section">
        @include('components.form._errors')
        <div class="form-container">
            <form action="{{ route('register') }}" action="POST">
                @csrf
                <!-- ROW -->
                <div class="form-row">
                    <!-- LEFT -->
                    <div class="form-left-col">
                        <label class="form-label" for="username">User name:</label>
                        <div class="form-sub-label">Required</div>
                    </div>
                    <!-- RIGHT -->
                    <div class="form-right-col">
                        <p class="form-label-phone">User name: <span class="form-sub-label">Required</span></p>
                        <div>
                            <input class="form-input" type="text" id="username" name="name" value="{{ old('name') }}"
                                required>
                        </div>
                    </div>
                </div>
                <!-- ROW -->
                <div class="form-row">
                    <!-- LEFT -->
                    <div class="form-left-col">
                        <label class="form-label" for="email">Email:</label>
                        <div class="form-sub-label">Required</div>
                    </div>
                    <!-- RIGHT -->
                    <div class="form-right-col">
                        <p class="form-label-phone">Email: <span class="form-sub-label">Required</span></p>
                        <div>
                            <input class="form-input" type="email" id="email" name="email" value="{{ old('email') }}"
                                autocomplete="email" required>
                        </div>
                    </div>
                </div>
                <!-- ROW -->
                <div class="form-row">
                    <!-- LEFT -->
                    <div class="form-left-col">
                        <label class="form-label" for="password">Password:</label>
                        <div class="form-sub-label">Required</div>
                    </div>
                    <!-- RIGHT -->
                    <div class="form-right-col">
                        <p class="form-label-phone">Password: <span class="form-sub-label">Required</span></p>
                        <div>
                            <input class="form-input" type="password" id="password" name="password" required
                                autocomplete="new-password">
                        </div>
                    </div>
                </div>
                <!-- ROW -->
                <div class="form-row">
                    <!-- LEFT -->
                    <div class="form-left-col">
                        <label class="form-label" for="confirm-password">Confirm Password:</label>
                        <div class="form-sub-label">Required</div>
                    </div>
                    <!-- RIGHT -->
                    <div class="form-right-col">
                        <p class="form-label-phone">Confirm Password: <span class="form-sub-label">Required</span></p>
                        <div>
                            <input class="form-input" type="password" id="confirm-password" name="password_confirmation"
                                required autocomplete="new-password">
                        </div>
                    </div>
                </div>


            </form>
        </div>
    </main>
</x-layouts._forums>