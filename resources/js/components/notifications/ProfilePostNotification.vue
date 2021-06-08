<template>
  <div>
    <profile-popover
      :user="postPoster"
      popover-classes="inline"
      triggerClasses="blue-link text-smaller mr-1/2"
      class="inline"
    ></profile-popover>
    <div class="text-black inline">
      <span>wrote a message on</span>
      <a class="blue-link">your profile</a>.
    </div>
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