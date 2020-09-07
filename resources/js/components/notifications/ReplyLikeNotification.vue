<template>
  <div>
    <a
      @click="showProfile(notificationData.liker)"
      class="blue-link notification-profile"
    >{{ notificationData.liker.name }}</a>
    <div @click="showReply(notificationData.reply)" class="inline notification-content w-full">
      liked
      <a @click="showThread(notificationData.reply.thread)" class="blue-link">your post</a>
      in the thread {{ notificationData.reply.thread.title }}
      <p class="text-xs text-gray-lightest">{{ notificationData.like.date_created }}</p>
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