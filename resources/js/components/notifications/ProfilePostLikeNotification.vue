<template>
  <div>
    <profile-popover
      class="inline"
      popover-classes="inline"
      :user="liker"
      triggerClasses="blue-link text-smaller"
    ></profile-popover>

    <div class="text-black inline">
      <span>liked</span>
      <a v-if="ownsPost(profilePost)" class="blue-link">your post</a>
      <a v-else class="blue-link">{{ poster }}'s post</a>
      on
      <span v-if="ownsProfile(profileOwner)">your profile</span>
      <span v-else>{{ profileOwner.name }}'s profile</span>.
    </div>
  </div>
</template>

<script>
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
  mixins: [authorizable],
};
</script>

<style lang="scss" scoped>
</style>