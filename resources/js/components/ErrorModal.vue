<template>
  <div>
    <modal name="error-modal" height="auto" width="40%">
      <div class="flex justify-between items-center p-3 bg-white-catskill">
        <p class="text-lg text-black-semi">Ops! we ran into some problems.</p>
        <button @click="hide" class="fas fa-times text-lg"></button>
      </div>
      <div v-for="(error, indexError) in errors">
        <div v-for="(message, indexMessage) in error">
          <li
            class="block p-3 text-black-semi text-sm list-disc"
            v-text="message"
          ></li>
        </div>
      </div>
    </modal>
  </div>
</template>

<script>
import EventBus from "../eventBus";
export default {
  data() {
    return {
      errors: [],
    };
  },
  methods: {
    showError(errors) {
      this.errors = errors;
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
    EventBus.$on("error", (errors) => {
      this.showError(errors);
    });
  },
};
</script>

<style lang="scss" scoped>
</style>