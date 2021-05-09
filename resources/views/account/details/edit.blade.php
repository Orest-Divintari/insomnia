<x-layouts.account section="details" title="Account details">

    <x-slot name="main">
        <div class="form-container">
            <form action="/account/details" method="POST">
                @method('patch')
                @csrf
                <div class=" border-white-catskill">
                    <div class="form-row">
                        <!-- LEFT -->
                        <div class="form-left-col py-4">
                            <label class="form-label" for="username">Username:</label>
                        </div>
                        <!-- RIGHT -->
                        <div class="form-right-col py-4">
                            <p class="form-label-phone">Username</p>
                            <div>
                                <input class="text-sm w-full bg-semi-white" value="{{ $user->name }}" type="text"
                                    id="username" name="name" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <!-- LEFT -->
                        <div class="form-left-col py-4">
                            <label class="form-label" for="email">Email:</label>
                        </div>
                        <!-- RIGHT -->
                        <div class="flex">
                            <div class="form-right-col py-4 pr-0">
                                <p class="form-label-phone">Email</p>
                                <div class="flex">
                                    <input class="text-sm bg-semi-white" value="{{ $user->email }}" type="text"
                                        id="email" name="email" disabled>
                                    <edit-email-modal :user="{{ $user }}"></edit-email-modal>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <!-- LEFT -->
                        <div class="form-left-col py-4">
                            <label class="form-label" for="avatar">Avatar:</label>
                        </div>
                        <!-- RIGHT -->
                        <div class="flex">
                            <div class="form-right-col py-4 pr-0">
                                <p class="form-label-phone">Avatar</p>
                                <div>
                                    <avatar avatar-classes="w-24 h-24" button-classes="w-24 h-12 text-xs"
                                        :user="{{ $user }}"> </avatar>
                                    <p class="mt-1 text-xs text-gray-lightest">Click the image to change the avatar</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="border-t-1 border-white-catskill">

                    <div class="form-row">
                        <!-- LEFT -->
                        <div class="form-left-col ">
                            <label class="form-label" for="birth_date">Date of birth:</label>
                        </div>
                        <!-- RIGHT -->
                        <div class="flex">
                            <div class="form-right-col  pr-0">
                                <p class="form-label-phone">Date of birth</p>
                                <div>
                                    <input type="date" name="birth_date" value="{{ $user->details()->birth_date }}"
                                        class="form-input" style="caret-color: transparent">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class=" form-row">
                        <!-- LEFT -->
                        <div class="form-left-col ">
                            <label class="form-label" for="location">Location:</label>
                        </div>
                        <!-- RIGHT -->
                        <div class="form-right-col">
                            <p class="form-label-phone">Location</p>
                            <div>
                                <input type="text" name="location" value="{{ $user->details()->location }}"
                                    class="form-input">
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <!-- LEFT -->
                        <div class="form-left-col ">
                            <label class="form-label" for="website">Website:</label>
                        </div>
                        <!-- RIGHT -->
                        <div class="form-right-col">
                            <p class="form-label-phone">Website</p>
                            <div>
                                <input type="text" name="website" value="{{ $user->details()->website }}"
                                    class="form-input">
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <!-- LEFT -->
                        <div class="form-left-col ">
                            <label class="form-label" for="gender">Gender:</label>
                        </div>
                        <!-- RIGHT -->
                        <div class="flex">
                            <div class="form-right-col ">
                                <p class="form-label-phone">Gender</p>

                                <div class="flex items-center">
                                    <input type="radio" name="gender" id="male" value="male" class=" mr-2 w-4 h-4"
                                        {{ $user->details()->gender == 'male' ? 'checked' : '' }}>
                                    <label class="text-sm text-black-semi" for="male">Male</label>
                                </div>
                                <div class="flex items-center">

                                    <input type="radio" name="gender" id="female" value="female" class="mr-2 w-4 h-4"
                                        {{ $user->details()->gender == 'female' ? 'checked' : '' }}>
                                    <label class="text-sm text-black-semi" for="female">Female</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="gender" id="no-selection" value="" class="mr-2 w-4 h-4"
                                        {{ !$user->details()->gender ? 'checked' : "" }}>
                                    <label class="text-sm text-black-semi" for="no-selection">No
                                        selection</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <!-- LEFT -->
                        <div class="form-left-col ">
                            <label class="form-label" for="occupation">Occupation:</label>
                        </div>
                        <!-- RIGHT -->
                        <div class="form-right-col">
                            <p class="form-label-phone">Occupation</p>
                            <div>
                                <input type="text" name="occupation" value="{{ $user->details()->occupation }}"
                                    class="form-input">
                            </div>
                        </div>
                    </div>

                    <hr class="border-t-1 border-white-catskill">

                    <div class="form-row">
                        <!-- LEFT -->
                        <div class="form-left-col ">
                            <label class="form-label" for="occupation">About you:</label>
                        </div>
                        <!-- RIGHT -->
                        <div class="form-right-col">
                            <p class="form-label-phone mt-2">About you:</p>
                            <div>
                                <wysiwyg name="about" value="{{ $user->details()->about }}"
                                    :style-attributes="'min-h-64'" required>
                                </wysiwyg>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="flex border border-t border-l-0 border-r-0 border-b border-white-catskill">
                    <div class="w-2/6 py-3  text-right px-4 text-lg">Identities</div>
                </div>

                <div class="form-row">
                    <!-- LEFT -->
                    <div class="form-left-col ">
                        <label class="form-label" for="skype">Skype:</label>
                    </div>
                    <!-- RIGHT -->
                    <div class="form-right-col">
                        <p class="form-label-phone">Skype</p>
                        <div>
                            <input type="text" name="skype" value="{{ $user->details()->skype }}" class="form-input">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <!-- LEFT -->
                    <div class="form-left-col ">
                        <label class="form-label" for="google_talk">Google talk:</label>
                    </div>
                    <!-- RIGHT -->
                    <div class="form-right-col">
                        <p class="form-label-phone">Google talk</p>
                        <div>
                            <input type="text" name="google_talk" value="{{ $user->details()->google_talk }}"
                                class="form-input">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <!-- LEFT -->
                    <div class="form-left-col ">
                        <label class="form-label" for="Facebook">Facebook:</label>
                    </div>
                    <!-- RIGHT -->
                    <div class="form-right-col">
                        <p class="form-label-phone">Facebook</p>
                        <div>
                            <input type="text" name="facebook" value="{{ $user->details()->facebook }}"
                                class="form-input">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <!-- LEFT -->
                    <div class="form-left-col ">
                        <label class="form-label" for="twitter">Twitter:</label>
                    </div>
                    <!-- RIGHT -->
                    <div class="form-right-col">
                        <p class="form-label-phone">Twitter</p>
                        <div>
                            <input type="text" name="twitter" value="{{ $user->details()->twitter }}"
                                class="form-input">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <!-- LEFT -->
                    <div class="form-left-col ">
                        <label class="form-label" for="instagram">Instagram:</label>
                    </div>
                    <!-- RIGHT -->
                    <div class="form-right-col">
                        <p class="form-label-phone">Instagram</p>
                        <div>
                            <input type="text" name="instagram" value="{{ $user->details()->instagram }}"
                                class="form-input">
                        </div>
                    </div>
                </div>




                <x-form.button class="text-xs" name="Save"></x-form.button>


            </form>
        </div>
    </x-slot>

</x-layouts.account>