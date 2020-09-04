<template>
  <div>
    <a
      class="text-blue-mid notification-profile"
      @click="showProfile(notificationData.commentPoster)"
    >{{ notificationData.commentPoster.name}}</a>

    <div @click="showPost" class="inline notification-content">
      <span class="no-underline hover:no-underline">commented on</span>
      <a v-if="ownsProfile() && ownsPost()" class="text-blue-mid">your status</a>
      <a v-if="!ownsProfile() && ownsPost()" class="text-blue-mid">your post</a>
      <a
        v-if="!ownsProfile() && !ownsPost()"
        class="text-blue-mid"
      >{{ notificationData.postPoster.name }}'s post</a>
      <div class="inline">
        <span v-if="ownsProfile()">on your profile</span>
        <span v-else>on {{ notificationData.profileOwner.name }}'s profile</span>
      </div>
      <p class="text-xs text-gray-lightest">{{ notificationData.profilePost.date_updated }}</p>
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
</script>F

<style lang="scss" scoped>
</style>