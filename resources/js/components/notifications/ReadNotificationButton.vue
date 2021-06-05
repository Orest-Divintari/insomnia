<template>
  <div
    class="text-blue-mid hover:text-blue-mid-light text-xs"
    @click.stop="toggleRead"
  >
    <button
      class="focus:outline-none"
      v-if="read && hovering"
      v-tooltip="{
        content: 'Mark unread',
        classes: ['read-notification'],
      }"
    >
      <i class="far fa-circle"></i>
    </button>
    <button
      class="focus:outline-none"
      v-if="!read"
      v-tooltip="{
        content: 'Mark read',
        classes: ['read-notification'],
      }"
    >
      <i class="fas fa-circle"></i>
    </button>
  </div>
</template>

<script>
export default {
  props: {
    hovering: {
      type: Boolean,
      required: true,
    },
    notification: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      read: this.notification.is_read,
    };
  },
  methods: {
    toggleRead() {
      this.read = !this.read;
      this.$emit("toggleRead");
    },
  },
};
</script>

<style lang="scss">
.tooltip {
  // ...

  &.read-notification {
    .tooltip-inner {
      background: black;
      opacity: 0.7;
      color: white;
      font-size: 0.75rem;
      padding: 0.4rem;
    }

    .tooltip-arrow {
      border-color: black;
    }
  }
}
</style>