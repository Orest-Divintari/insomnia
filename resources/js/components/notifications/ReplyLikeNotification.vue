<template>
  <div class="flex">
    <profile-popover
      class="mr-2"
      :user="notificationData.liker"
      trigger="avatar"
      triggerClasses="avatar-sm"
    >
    </profile-popover>
    <div class="flex-1">
      <profile-popover
        :user="notificationData.liker"
        popover-classes="inline"
        triggerClasses="blue-link text-smaller notification-profile mr-1/2"
        class="inline"
      ></profile-popover>
      <div
        @click="showReply(notificationData.reply)"
        class="inline notification-content w-full"
      >
        liked
        <a @click="showThread(notificationData.reply.thread)" class="blue-link"
          >your post</a
        >
        in the thread {{ notificationData.reply.repliable.title }}
        <p class="text-xs text-gray-lightest">
          {{ notificationData.like.date_created }}
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
  mixins: [view],
};
</script>

<style lang="scss" scoped>
</style>