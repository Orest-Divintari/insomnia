<template>
  <div>
    <profile-popover
      :user="liker"
      popover-classes="inline"
      triggerClasses="blue-link text-smaller  mr-1/2"
      class="inline"
    ></profile-popover>
    <p class="inline w-full">
      liked
      <a class="blue-link">your post</a>
      in the thread {{ reply.repliable.title }}
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