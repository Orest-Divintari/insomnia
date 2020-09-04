<template>
  <div>
    <a
      @click="showProfile(notificationData.liker)"
      class="text-blue-mid notification-profile"
    >{{ notificationData.liker.name }}</a>
    <div
      @click="showPost(notificationData.profileOwner, notificationData.profilePost)"
      class="text-black inline notification-content"
    >
      <span>liked</span>
      <a v-if="ownsComment()" class="text-blue-mid">your comment</a>
      <a v-else class="text-blue-mid">{{ notificationData.commentPoster }}'s comment</a>
      on
      <span v-if="ownsProfile()">your profile</span>
      <span v-else>{{ notificationData.profileOwner.name }}'s profile</span>.
      <p class="text-xs text-gray-lightest">{{ notificationData.like.date_created }}</p>
    </div>
  </div>
</template>

<script>
import notifications from "../../mixins/notifications";
import authorization from "../../mixins/authorization";
export default {
  props: {
    notificationData: {
      type: Object,
      default: {},
    },
  },
  mixins: [notifications, authorization],
};
</script>

<style lang="scss" scoped>
</style>