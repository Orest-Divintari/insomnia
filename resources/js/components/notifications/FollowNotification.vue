<template>
  <div class="flex">
    <profile-popover
      class="mr-2"
      :user="follower"
      trigger="avatar"
      triggerClasses="avatar-sm"
    >
    </profile-popover>

    <div>
      <p class="flex space-x-1 items-center">
        <profile-popover
          :user="follower"
          triggerClasses="text-blue-link text-smaller"
        >
        </profile-popover>
        <span> is now following you.</span>
        <p class="block text-xs text-gray-lightest">
          {{ notificationData.follow_date_created }}
        </p>
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
  data() {
    return {
      following: this.notificationData.following,
      follower: this.notificationData.follower,
    };
  },
};
</script>

<style lang="scss" scoped>
</style>