<template>
  <div>
    <div class="relative" v-click-outside="hide">
      <div @click="toggle">
        <slot name="dropdown-trigger"></slot>
      </div>
      <div
        @click="hideOnClick ? hide() : null"
        v-if="isOpen"
        class="z-10 dropdown-menu"
        :class="styleClasses"
      >
        <slot name="dropdown-items"></slot>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    styleClasses: {
      type: String,
      default: "",
    },
    hideOnClick: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      isOpen: false,
    };
  },
  methods: {
    toggle() {
      this.isOpen = !this.isOpen;
    },
    hide() {
      this.isOpen = false;
    },
  },
};
</script>

<style lang="scss" scoped>
</style>