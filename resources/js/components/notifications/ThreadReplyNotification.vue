<template>
  <div>
    <a
      @click="showProfile(notificationData.reply.poster)"
      class="text-blue-mid notification-profile"
    >{{ notificationData.reply.poster.name}}</a>
    <div @click="showReply()" class="inline notification-content w-full">
      replied to the thtread -
      <a class="text-blue-mid">{{ notificationData.thread.title }}</a>.
      <p class="text-xs text-gray-lightest">{{ notificationData.thread.date_updated }}</p>
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