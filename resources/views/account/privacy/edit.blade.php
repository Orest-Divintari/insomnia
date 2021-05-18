<x-layouts.account title="Privacy" section="privacy">
    <x-slot name="main">
        <x-form.errors></x-form.errors>
        <form action="/account/privacy" method="POST" class="form-container" v-cloak>
            @method('patch')
            @csrf

            <div class="form-row">
                <!-- LEFT -->
                <div class="form-left-col">
                    <label class="form-label" for="privacy-options">Privacy options:</label>
                </div>
                <!-- RIGHT -->
                <div class="form-right-col mt-5/2">
                    <p class="form-label-phone">Privacy options:</p>
                    <div class="flex flex-row-reverse items-center justify-end">
                        <label for="current-activity" class="text-black-semi text-sm">Show your current
                            activity</label>
                        <input value="true" class="mt-1/2 mr-2" type="checkbox" id="current-activity"
                            name="show_current_activity" {{ $user->privacy()->show_current_activity ? 'checked' : '' }}>
                    </div>
                    <p class="text-gray-shuttle text-smaller ml-5.5">This will allow other people to see what page you
                        are
                        currently viewing.</p>
                    <birth-date-checkbox :birth-date="{{ json_encode($user->privacy()->show_birth_date)}}"
                        :birth-year="{{ json_encode($user->privacy()->show_birth_year) }}">
                    </birth-date-checkbox>


                </div>
            </div>

            <div class="form-row text-sm">
                <!-- LEFT -->
                <div class="form-left-col">
                    <label class="form-label" for="allow-users">Allow users to:</label>
                </div>
                <!-- RIGHT -->
                <div class="form-right-col">
                    <p class="form-label-phone">Allow users to:</p>
                    <div class="flex justify-between items-center">
                        <label for="" class="text-sm text-black-semi">View your details on your profile page:
                        </label>
                        <select name="show_details" id=""
                            class="focus:outline-none border border-gray-lighter rounded p-2">
                            <option {{ $user->privacy()->show_details == 'members' ? 'selected' : '' }} value="members">
                                Members only</option>
                            <option {{ $user->privacy()->show_details == 'following' ? 'selected' : '' }}
                                value="following">People you follow</option>
                            <option {{ $user->privacy()->show_details == 'noone' ? 'selected' : '' }} value="noone">
                                Nobody</option>
                        </select>
                    </div>

                    <div class="flex justify-between items-center mt-2">
                        <label for="" class="text-sm text-black-semi">Post messages on your profile page:
                        </label>
                        <select name="post_on_profile" id=""
                            class="focus:outline-none border border-gray-lighter rounded p-2">
                            <option {{ $user->privacy()->post_on_profile  == 'members' ? 'selected' : '' }}
                                value="members">Members only</option>
                            <option {{ $user->privacy()->post_on_profile  == 'following' ? 'selected' : '' }}
                                value="following">People you follow</option>
                            <option {{ $user->privacy()->post_on_profile  == 'noone' ? 'selected' : '' }} value="noone">
                                Nobody</option>
                        </select>
                    </div>

                    <div class="flex justify-between items-center mt-2">
                        <label for="" class="text-sm text-black-semi">Start conversations with you: </label>
                        <select name="start_conversation" id=""
                            class="focus:outline-none border border-gray-lighter rounded p-2">
                            <option {{ $user->privacy()->start_conversation  == 'members' ? 'selected' : '' }}
                                value="members">Members only</option>
                            <option {{ $user->privacy()->start_conversation  == 'following' ? 'selected' : '' }}
                                value="following">People you follow</option>
                            <option {{ $user->privacy()->start_conversation  == 'noone' ? 'selected' : '' }}
                                value="noone">Nobody</option>
                        </select>
                    </div>

                    <div class="flex justify-between items-center mt-2">
                        <label for="" class="text-sm text-black-semi">View your identities: </label>
                        <select name="show_identities" id=""
                            class="focus:outline-none border border-gray-lighter rounded p-2">
                            <option {{ $user->privacy()->show_identities  == 'members' ? 'selected' : '' }}
                                value="members">
                                Members only</option>
                            <option {{ $user->privacy()->show_identities  == 'following' ? 'selected' : '' }}
                                value="following">People you follow</option>
                            <option {{ $user->privacy()->show_identities  == 'noone' ? 'selected' : '' }} value="noone">
                                Nobody</option>
                        </select>
                    </div>
                </div>
            </div>

            <x-form.button name="Save"></x-form.button>
        </form>
    </x-slot>
</x-layouts.account>