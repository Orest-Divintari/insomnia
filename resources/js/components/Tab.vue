<template>
  <div v-if="isActive">
    <slot></slot>
  </div>
</template>

<script>
export default {
  props: {
    name: {
      type: String,
      default: "",
      required: true
    },
    selected: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      isActive: false
    };
  },

  computed: {
    hrefDescription() {
      return this.name
        .toLowerCase()
        .split(" ")
        .join("-");
    }
  },
  mounted() {
    this.isActive = this.selected;
  }
};
</script>

<style lang="scss" scoped>
</style>