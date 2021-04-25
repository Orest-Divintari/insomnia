<template>
  <div class="flex">
    <profile-popover
      class="mr-2"
      :user="liker"
      trigger="avatar"
      triggerClasses="avatar-sm"
    >
    </profile-popover>
    <div class="flex-1">
      <profile-popover
        :user="liker"
        popover-classes="inline"
        triggerClasses="blue-link text-smaller notification-profile mr-1/2"
        class="inline"
      ></profile-popover>
      <div @click="showReply(reply)" class="inline notification-content w-full">
        liked
        <a @click="showThread(reply.thread)" class="blue-link">your post</a>
        in the thread {{ reply.repliable.title }}
        <p class="text-xs text-gray-lightest">
          {{ like.date_created }}
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import view from "../../mixins/view";
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
  mixins: [view],
};
</script>

<style lang="scss" scoped>
</style>