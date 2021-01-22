<template>
  <div class="flex">
    <profile-popover
      class="mr-2"
      :user="notificationData.reply.poster"
      trigger="avatar"
      triggerClasses="avatar-sm"
    >
    </profile-popover>
    <div class="flex-1">
      <profile-popover
        :user="notificationData.reply.poster"
        popover-classes="inline"
        triggerClasses="blue-link text-smaller notification-profile mr-1/2"
        class="inline"
      ></profile-popover>
      <div
        @click="showReply(notificationData.reply)"
        class="inline notification-content w-full"
      >
        replied to the thtread -
        <a class="blue-link">{{ notificationData.thread.title }}</a
        >.
        <p class="text-xs text-gray-lightest">
          {{ notificationData.thread.date_updated }}
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