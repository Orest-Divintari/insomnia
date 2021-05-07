<template>
  <div class="flex">
    <profile-popover
      class="mr-5/2"
      :user="commentPoster"
      trigger="avatar"
      triggerClasses="avatar-sm"
    >
    </profile-popover>
    <div class="flex-1">
      <profile-popover
        :user="commentPoster"
        popover-classes="inline"
        triggerClasses="blue-link text-smaller notification-profile mr-1/2"
        class="inline"
      ></profile-popover>
      <div @click="showComment(comment)" class="inline notification-content">
        <span class="no-underline hover:no-underline">commented on</span>
        <a
          v-if="isAuthUser(profileOwner) && ownsPost(profilePost)"
          class="blue-link"
          >your status</a
        >
        <a
          v-if="!isAuthUser(profileOwner) && ownsPost(profilePost)"
          class="blue-link"
          >your post</a
        >
        <a
          v-if="isAuthUser(profileOwner) && !ownsPost(profilePost)"
          class="blue-link"
          >{{ postPoster.name }}'s post</a
        >
        <div class="inline">
          <span v-if="isAuthUser(profileOwner)">on your profile</span>
          <span v-else>on {{ profileOwner.name }}'s profile</span>
        </div>
        <p class="text-xs text-gray-lightest">
          {{ profilePost.date_created }}
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import view from "../../mixins/view";
import authorization from "../../mixins/authorization";
export default {
  props: {
    notificationData: {
      type: Object,
      default: {},
    },
  },
  mixins: [view, authorization],
  data() {
    return {
      ...this.notificationData,
    };
  },
};
</script>F

<style lang="scss" scoped>
</style>