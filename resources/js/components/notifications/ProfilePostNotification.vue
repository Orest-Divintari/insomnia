<template>
  <div>
    <a
      class="blue-link notification-profile"
      @click="showProfile(notificationData.postPoster)"
    >{{ notificationData.postPoster.name}}</a>
    <div @click="showPost()" class="text-black inline notification-content">
      <span>wrote a message on</span>
      <a
        @click="showProfile(notificationData.profileOwner, notificationData.profilePost)"
        class="blue-link"
      >your profile</a>.
      <p class="text-xs text-gray-lightest">{{ notificationData.profilePost.date_created }}</p>
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