<template>
  <div class="flex">
    <profile-popover
      class="mr-5/2"
      :user="postPoster"
      trigger="avatar"
      triggerClasses="avatar-sm"
    >
    </profile-popover>
    <div class="flex-1">
      <profile-popover
        :user="postPoster"
        popover-classes="inline"
        triggerClasses="blue-link text-smaller notification-profile mr-1/2"
        class="inline"
      ></profile-popover>
      <div class="text-black inline notification-content">
        <span>wrote a message on</span>
        <a @click="showPost(profilePost)" class="blue-link">your profile</a>.
        <p class="text-xs text-gray-lightest">
          {{ profilePost.date_created }}
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
  data() {
    return {
      ...this.notificationData,
    };
  },
  mixins: [view],
  methods: {},
};
</script>

<style lang="scss" scoped>
</style>