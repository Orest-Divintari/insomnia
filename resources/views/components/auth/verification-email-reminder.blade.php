<div class="flex border border-blue-mid-dark rounded mt-2 mb-4" v-cloak>
    <div class="bg-blue-mid-dark py-4 px-3 text ">
        <i class="fas fa-exclamation-circle text-xl text-gray-400 align-middle pt-1"></i>
    </div>
    <div>
        <p class="p-3 text-sm">
            Your account is currently awaiting verification. Verification was sent to {{ auth()->user()->email }}
            <resend-verification-email-button :user="{{ auth()->user() }}"
                recaptcha-site-key="{{config('insomnia.recaptcha.site_key')}}">
                </send-verificaiton-email-button>
        </p>
    </div>
</div>