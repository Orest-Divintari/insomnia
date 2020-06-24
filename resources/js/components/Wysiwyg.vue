<template>
  <div>
    <input :value="value" :name="name" id="trix" type="hidden" />
    <trix-editor
      class="border border-blue-light"
      :class="classes"
      input="trix"
      ref="trix"
      :placeholder="placeholder"
    ></trix-editor>
  </div>
</template>

<script>
import Trix from "trix";
export default {
  props: {
    name: {
      type: String,
      default: ""
    },
    value: {
      type: String,
      default: ""
    },
    placeholder: {
      type: String,
      default: " "
    },
    clearInput: {
      type: Boolean,
      default: false
    },
    classes: {
      type: String,
      default: ""
    }
  },
  mounted() {
    this.$refs.trix.addEventListener("trix-change", e => {
      this.$emit("input", e.target.innerHTML);
    });
  },
  watch: {
    clearInput() {
      this.$refs.trix.value = "";
    }
  }
};
</script>

<style lang="scss" scoped>
</style>