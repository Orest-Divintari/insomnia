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
        <a v-if="ownsComment(comment)" class="blue-link">your comment</a>
        <a v-else class="blue-link">{{ commentPoster }}'s comment</a>
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
import authorization from "../../mixins/authorization";
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
  mixins: [view, authorization],
};
</script>

<style lang="scss" scoped>
</style>