<template>
  <div>
    <a
      @click="showProfile(notificationData.liker)"
      class="text-blue-mid notification-profile"
    >{{ notificationData.liker.name }}</a>
    <div @click="showReply()" class="inline notification-content w-full">
      liked
      <a class="text-blue-mid">your post</a>
      in the thread {{ notificationData.reply.thread.title }}
      <p class="text-xs text-gray-lightest">{{ notificationData.like.date_created }}</p>
    </div>
  </div>
</template>

<script>
import notifications from "../../mixins/notifications";
export default {
  props: {
    notificationData: {
      type: Object,
      default: {},
    },
  },
  mixins: [notifications],
};
</script>

<style lang="scss" scoped>
</style>