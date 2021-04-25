<template>
  <div class="flex">
    <profile-popover
      class="mr-2"
      :user="notificationData.postPoster"
      trigger="avatar"
      triggerClasses="avatar-sm"
    >
    </profile-popover>
    <div class="flex-1">
      <profile-popover
        :user="notificationData.postPoster"
        popover-classes="inline"
        triggerClasses="blue-link text-smaller notification-profile mr-1/2"
        class="inline"
      ></profile-popover>
      <div class="text-black inline notification-content">
        <span>wrote a message on</span>
        <a @click="showPost(notificationData.profilePost)" class="blue-link"
          >your profile</a
        >.
        <p class="text-xs text-gray-lightest">
          {{ notificationData.profilePost.date_created }}
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
  methods: {},
};
</script>

<style lang="scss" scoped>
</style>