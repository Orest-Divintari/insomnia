<template>
  <div>
    <profile-popover
      :user="reply.poster"
      popover-classes="inline"
      triggerClasses="blue-link text-smaller mr-1/2"
      class="inline"
    ></profile-popover>
    <p class="inline">
      replied to the thtread -
      <a class="blue-link">{{ thread.title }}</a
      >.
    </p>
  </div>
</template>

<script>
export default {
  props: {
    notificationData: {
      type: Object,
      default: {},
    },
    hovering: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      ...this.notificationData,
    };
  },
};
</script>

<style lang="scss" scoped>
</style>