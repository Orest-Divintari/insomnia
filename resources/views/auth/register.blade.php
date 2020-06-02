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
            <form action="">

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
                            <input class="form-input" type="text" id="username" name="username" required>
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
                            <input class="form-input" type="email" id="email" name="email" required>
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
                            <input class="form-input" type="password" id="password" name="password" required>
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
                            <input class="form-input" type="password" id="confirm-password" name="confirm-password"
                                required>
                        </div>
                    </div>
                </div>
                <!-- ROW -->
                <div class="form-row">
                    <!-- LEFT -->
                    <div class="form-left-col">
                        <label class="form-label" for="verification">Verification:</label>
                        <div class="form-sub-label">Required</div>
                    </div>
                    <!-- RIGHT -->
                    <div class="form-right-col">
                        <div class="g-recaptcha" data-sitekey="{{config('insomnia.recaptcha.site_key')}}"></div>
                    </div>
                </div>
                <x-form._button name="Register"></x-form._button>

            </form>
        </div>
    </main>
</x-layouts._forums>