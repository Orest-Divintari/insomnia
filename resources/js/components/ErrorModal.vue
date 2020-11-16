<template>
  <div>
    <modal name="error-modal" height="auto" width="40%">
      <div class="flex justify-between items-center p-3 bg-white-catskill">
        <p class="text-lg text-black-semi">Ops! we ran into some problems.</p>
        <button @click="hide" class="fas fa-times text-lg"></button>
      </div>
      <p class="block p-3 text-black-semi text-sm" v-text="message"></p>
    </modal>
  </div>
</template>

<script>
import EventBus from "../eventBus";
export default {
  data() {
    return {
      message: "",
    };
  },
  methods: {
    flash(message) {
      this.message = message;
      this.show();
    },
    show() {
      this.$modal.show("error-modal");
    },
    hide() {
      this.$modal.hide("error-modal");
    },
  },
  created() {
    EventBus.$on("error", (message) => {
      this.flash(message);
    });
  },
};
</script>

<style lang="scss" scoped>
</style>