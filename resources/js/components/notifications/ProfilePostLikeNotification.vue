<template>
  <div class="flex">
    <profile-popover
      class="mr-5/2"
      :user="liker"
      trigger="avatar"
      triggerClasses="avatar-sm"
    >
    </profile-popover>
    <div class="flex-1">
      <profile-popover
        class="inline"
        popover-classes="inline"
        :user="liker"
        triggerClasses="blue-link notification-profile text-smaller"
      ></profile-popover>

      <div
        @click="showPost(profilePost)"
        class="text-black inline notification-content"
      >
        <span>liked</span>
        <a v-if="ownsPost(profilePost)" class="blue-link">your post</a>
        <a v-else class="blue-link">{{ poster }}'s post</a>
        on
        <span v-if="ownsProfile(profileOwner)">your profile</span>
        <span v-else>{{ profileOwner.name }}'s profile</span>.
        <p class="text-xs text-gray-lightest">
          {{ like.date_created }}
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import view from "../../mixins/view";
import authorizable from "../../mixins/authorizable";
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
  mixins: [view, authorizable],
};
</script>

<style lang="scss" scoped>
</style>