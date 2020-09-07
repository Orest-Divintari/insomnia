<template>
  <div>
    <a
      @click="showProfile(notificationData.liker)"
      class="blue-link notification-profile"
    >{{ notificationData.liker.name }}</a>
    <div
      @click="showPost(notificationData.profileOwner, notificationData.profilePost)"
      class="text-black inline notification-content"
    >
      <span>liked</span>
      <a v-if="ownsComment(notificationData.comment)" class="blue-link">your comment</a>
      <a v-else class="blue-link">{{ notificationData.commentPoster }}'s comment</a>
      on
      <span v-if="ownsProfile(notificationData.profileOwner)">your profile</span>
      <span v-else>{{ notificationData.profileOwner.name }}'s profile</span>.
      <p class="text-xs text-gray-lightest">{{ notificationData.like.date_created }}</p>
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
};
</script>

<style lang="scss" scoped>
</style>