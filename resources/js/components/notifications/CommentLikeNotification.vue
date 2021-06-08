<template>
  <div>
    <profile-popover
      class="inline"
      popover-classes="inline"
      :user="liker"
      triggerClasses="blue-link text-smaller"
    ></profile-popover>

    <div @click="showPost(profilePost)" class="text-black inline">
      <span>liked</span>
      <a v-if="ownsComment(comment)" class="blue-link">your comment</a>
      <a v-else class="blue-link">{{ commentPoster }}'s comment</a>
      on
      <span v-if="ownsProfile(profileOwner)">your profile</span>
      <span v-else>{{ profileOwner.name }}'s profile</span>.
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