<template>
  <div>
    <input :value="value" :name="name" id="trix" type="hidden" />
    <trix-editor
      class="border border-blue-light trix-content"
      :class="classes"
      input="trix"
      ref="trix"
      :placeholder="placeholder"
    ></trix-editor>
  </div>
</template>

<script>
import EventBus from "../eventBus";
import Trix from "trix";
export default {
  props: {
    name: {
      type: String,
      default: "",
    },
    value: {
      type: String,
      default: "",
    },
    placeholder: {
      type: String,
      default: " ",
    },
    classes: {
      type: String,
      default: "",
    },
  },
  methods: {
    clearInput() {
      this.$refs.trix.value = "";
    },
  },
  mounted() {
    this.$refs.trix.addEventListener("trix-change", (e) => {
      this.$emit("input", e.target.innerHTML);
    });

    EventBus.$on("newReply", this.clearInput);
  },
};
</script>

<style lang="scss" scoped>
</style>