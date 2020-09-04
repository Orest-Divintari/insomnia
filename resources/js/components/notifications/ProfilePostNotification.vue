<template>
  <div>
    <a
      class="text-blue-mid notification-profile"
      @click="showProfile(notificationData.postPoster)"
    >{{ notificationData.postPoster.name}}</a>
    <div @click="showPost()" class="text-black inline notification-content">
      <span>wrote a message on</span>
      <a @click="showProfile(notificationData.profileOwner)" class="text-blue-mid">your profile</a>.
      <p class="text-xs text-gray-lightest">{{ notificationData.profilePost.date_created }}</p>
    </div>
  </div>
</template>

<script>
import notifications from "../../mixins/notifications";
export default {
  props: {
    notificationData: {
      type: Object,
      default: {},
    },
  },
  mixins: [notifications],
  methods: {},
};
</script>

<style lang="scss" scoped>
</style>