<template>
  <div>
    <a
      @click="showProfile(notificationData.reply.poster)"
      class="blue-link notification-profile"
    >{{ notificationData.reply.poster.name}}</a>
    <div @click="showReply(notificationData.reply)" class="inline notification-content w-full">
      replied to the thtread -
      <a class="blue-link">{{ notificationData.thread.title }}</a>.
      <p class="text-xs text-gray-lightest">{{ notificationData.thread.date_updated }}</p>
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