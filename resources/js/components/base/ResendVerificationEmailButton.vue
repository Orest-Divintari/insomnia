<template>
  <div v-cloak>
    <button @click="showModal" class="blue-link">
      Resend confirmation email
    </button>
    <modal
      @closed="hideModal"
      width="800px"
      height="auto"
      name="resend-verification-email-modal"
    >
      <div class="form-container">
        <div
          class="
            flex
            justify-between
            items-center
            bg-blue-light
            text-lg text-black-semi
            border-b border-blue-light
            py-3
            px-3
          "
        >
          <p>Resend account verification</p>
          <button @click="hideModal" class="fas fa-times"></button>
        </div>
        <p class="p-3 text-small">
          Are you sure you want to resend the account verification email? Any
          previous account verification emails will no longer function. This
          email will be sent to {{ user.email }}
        </p>
        <hr class="bg-blue-lighter" />
        <form>
          <!-- ROW -->
          <div class="form-row">
            <!-- LEFT -->
            <div class="form-left-col">
              <label class="form-label" for="verification">Verification:</label>
            </div>
            <!-- RIGHT -->
            <div class="form-right-col">
              <p class="form-label-phone">Verification:</p>
              <vue-recaptcha
                @verify="onVerify"
                :loadRecaptchaScript="true"
                :sitekey="recaptchaSiteKey"
              ></vue-recaptcha>
            </div>
          </div>
          <div class="form-button-container justify-center">
            <button @click="sendEmail" type="button" class="form-button">
              Resend email
            </button>
          </div>
        </form>
      </div>
    </modal>
  </div>
</template>

<script>
import VueRecaptcha from "vue-recaptcha";
export default {
  name: "ResendVerificationEmailButton",
  components: {
    VueRecaptcha,
  },
  props: {
    user: {
      type: Object,
      required: true,
    },
    recaptchaSiteKey: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      recaptchaResponse: "",
    };
  },

  methods: {
    name() {},
    onVerify(response) {
      this.recaptchaResponse = response;
    },
    sendEmail() {
      axios
        .post("/ajax/verification-email", {
          "g-recaptcha-response": this.recaptchaResponse,
        })
        .then((response) => this.onSuccess(response))
        .catch((error) => console.log(error));
    },
    hideModal() {
      this.$modal.hide("resend-verification-email-modal");
    },
    showModal() {
      this.$modal.show("resend-verification-email-modal");
    },
    onSuccess(response) {
      this.hideModal();
    },
  },
};
</script>

<style lang="scss" scoped>
</style>