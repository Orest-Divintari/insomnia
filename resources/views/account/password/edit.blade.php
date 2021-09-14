<x-layouts.account section="password and security" title="Password and security">
    <x-slot name="main">
        <x-form.errors></x-form.errors>
        <form action="/account/password" method="POST" class="form-container" v-cloak>
            @method('patch')
            @csrf
            <div class="form-row">
                <!-- LEFT -->
                <div class="form-left-col">
                    <label class="form-label" for="current-password">Your existing password:</label>
                </div>
                <!-- RIGHT -->
                <div class="form-right-col py-4">
                    <p class="form-label-phone">Your existing password:</p>
                    <password-input name="current_password"></password-input>
                    <p class="text-gray-shuttle text-smaller mt-2 leading-4">For security reasons, you must
                        verify your
                        existing
                        password before you may set a new password.</p>
                </div>
            </div>

            <div class="form-row">
                <!-- LEFT -->
                <div class="form-left-col">
                    <label class="form-label" for="password">New password:</label>
                </div>
                <!-- RIGHT -->
                <div class="form-right-col py-4">
                    <p class="form-label-phone">New password:</p>
                    <password-input name="password"></password-input>
                </div>
            </div>

            <div class="form-row">
                <!-- LEFT -->
                <div class="form-left-col">
                    <label class="form-label" for="password-confirmation">Confirm new password:</label>
                </div>
                <!-- RIGHT -->
                <div class="form-right-col py-4">
                    <p class="form-label-phone">Confirm new password:</p>
                    <password-input name="password_confirmation"></password-input>
                </div>
            </div>
            <x-form.button name="Save"></x-form.button>
        </form>

    </x-slot>
</x-layouts.account>