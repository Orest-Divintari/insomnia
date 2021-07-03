<template>
  <div dusk="watch-thread-modal">
    <modal name="watch-thread" height="auto" @closed="onClose">
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
          <p>Watch thread</p>
          <button @click="hideModal" class="fas fa-times"></button>
        </div>
        <form @submit.prevent>
          <!-- ROW -->
          <div class="form-row">
            <!-- LEFT -->
            <div class="form-left-col">
              <label class="form-label" for>Watch this thread:</label>
            </div>
            <!-- RIGHT -->
            <div class="form-right-col">
              <p class="form-label-phone">Watch this thread...:</p>
              <div>
                <div class="flex flex-row-reverse items-center">
                  <label for="enable_emails" class="form-label flex-1 ml-2"
                    >and receive email notifications</label
                  >
                  <input
                    type="radio"
                    id="enable_emails"
                    ref="enable"
                    name="email_notifications"
                    value="true"
                    dusk="with-email-notifications-radio-button"
                    checked
                  />
                </div>
                <div class="flex flex-row-reverse items-center">
                  <label for="disable_email" class="form-label flex-1 ml-2"
                    >without receiving email notifications</label
                  >
                  <input
                    type="radio"
                    id="disable_emails"
                    name="email_notifications"
                    dusk="without-email-notifications-radio-button"
                    value="false"
                  />
                </div>
              </div>
            </div>
          </div>
          <div class="form-button-container justify-center">
            <button
              dusk="modal-watch-button"
              @click="watch"
              type="submit"
              class="form-button"
            >
              Watch
            </button>
          </div>
        </form>
      </div>
    </modal>
  </div>
</template>

<script>
export default {
  props: {
    showWatchModal: {
      type: Boolean,
      default: false,
    },
  },

  watch: {
    showWatchModal() {
      this.showWatchModal ? this.showModal() : this.hideModal();
    },
  },
  methods: {
    onClose() {
      this.$emit("closed");
    },
    showModal() {
      this.$modal.show("watch-thread");
    },
    hideModal() {
      this.$modal.hide("watch-thread");
    },
    watch() {
      var mailNotifications = {
        email_notifications: this.$refs.enable.checked,
      };

      this.$emit("watch", mailNotifications);
      this.hideModal();
    },
  },
};
</script>

<style lang="scss" scoped>
</style>