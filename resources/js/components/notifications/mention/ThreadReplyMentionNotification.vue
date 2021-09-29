<template>
  <div>
    <profile-popover
      :user="triggerer"
      popover-classes="inline"
      triggerClasses="blue-link text-smaller  mr-1/2"
      class="inline"
    ></profile-popover>
    <div class="inline">
      <span class="no-underline hover:no-underline">mentioned you in</span>
      <a class="blue-link">a reply</a>
      in thread {{ thread.title }}
    </div>
  </div>
</template>

<script>
export default {
  name: "ThreadMentionNotification",
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