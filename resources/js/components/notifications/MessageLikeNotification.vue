<template>
  <div>
    <div class="flex items-start">
      <img
        @click="showProfile(notificationData.liker)"
        :src="notificationData.liker.avatar_path"
        class="avatar-sm mr-2"
      />
      <div>
        <a
          @click="showProfile(notificationData.liker)"
          class="blue-link notification-profile mr-1/2"
          >{{ notificationData.liker.name }}</a
        >
        <span class="group" @click="showMessage(notificationData.message)">
          liked your message in the conversation
          <a class="group-hover:underline blue-link">
            {{ notificationData.conversation.title }}
          </a>
          <p class="block text-xs text-gray-lightest">
            {{ notificationData.like.date_created }}
          </p>
        </span>
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