<template>
  <div>
    <profile-popover
      :user="commentPoster"
      popover-classes="inline"
      triggerClasses="blue-link text-smaller  mr-1/2"
      class="inline"
    ></profile-popover>
    <div class="inline">
      <span class="no-underline hover:no-underline">commented on</span>
      <a
        v-if="
          isAuthUser(profileOwner) &&
          ownsPost(profilePost) &&
          belongsToProfile(profilePost, profileOwner)
        "
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
        <span v-if="isAuthUser(profileOwner) && !ownsPost(profilePost)"
          >on your profile</span
        >
        <span v-if="!belongsToProfile(profilePost, profileOwner)"
          >on {{ profileOwner.name }}'s profile</span
        >
      </div>
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
  mixins: [authorizable],
  data() {
    return {
      ...this.notificationData,
    };
  },
  methods: {
    belongsToProfile(post, user) {
      return post.profile_owner_id == user.id;
    },
  },
};
</script>

<style lang="scss" scoped>
</style>