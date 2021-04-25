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

      <span class="group" @click="showMessage(message)">
        liked your message in the conversation
        <a class="group-hover:underline blue-link">
          {{ conversation.title }}
        </a>
        <p class="block text-xs text-gray-lightest">
          {{ like.date_created }}
        </p>
      </span>
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