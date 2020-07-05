<template>
  <div>
    <modal name="unwatch-thread" height="auto">
      <div class="form-container">
        <div
          class="flex justify-between items-center bg-blue-light text-lg text-black-semi border-b border-blue-light py-3 px-3"
        >
          <p>Unwatch thread</p>
          <button @click="hideModal" class="fas fa-times"></button>
        </div>
        <!-- ROW -->
        <div class="form-row">
          <!-- LEFT -->
          <div
            class="w-full py-5 text-center form-label"
          >Are you sure you want to unwatch this thread ?</div>
        </div>
        <div class="form-button-container justify-center">
          <button @click="unwatch" class="form-button">Unwatch</button>
        </div>
      </div>
    </modal>
  </div>
</template>

<script>
export default {
  props: {
    showUnwatchModal: {
      type: Boolean,
      default: false
    }
  },
  watch: {
    showUnwatchModal() {
      this.showUnwatchModal ? this.showModal() : this.hideModal();
    }
  },
  methods: {
    showModal() {
      this.$modal.show("unwatch-thread");
    },
    hideModal() {
      this.$modal.hide("unwatch-thread");
    },
    unwatch() {
      this.hideModal();
      this.$emit("unwatch");
    }
  }
};
</script>

<style lang="scss" scoped>
</style>