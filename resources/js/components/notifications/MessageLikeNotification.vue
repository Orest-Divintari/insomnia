<template>
  <div>
    <a
      @click="showProfile(notificationData.liker)"
      class="blue-link notification-profile"
      >{{ notificationData.liker.name }}</a
    >
    <div
      @click="showMessage(notificationData.message)"
      class="inline notification-content w-full"
    >
      liked your message
      <a class="blue-link">{{ notificationData.conversation.title }}</a>
      <p class="text-xs text-gray-lightest">
        {{ notificationData.like.date_created }}
      </p>
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