<template>
  <div>
    <profile-popover
      :user="liker"
      popover-classes="inline"
      triggerClasses="blue-link text-smaller notification-profile mr-1/2"
      class="inline"
    ></profile-popover>
    <p class="inline">
      liked your message in the conversation
      <a class="blue-link">
        {{ conversation.title }}
      </a>
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