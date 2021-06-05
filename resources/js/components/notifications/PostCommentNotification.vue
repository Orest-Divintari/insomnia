<template>
  <div class="flex">
    <profile-popover
      class="mr-5/2"
      :user="commentPoster"
      trigger="avatar"
      triggerClasses="avatar-sm"
    >
    </profile-popover>
    <div class="flex-1">
      <profile-popover
        :user="commentPoster"
        popover-classes="inline"
        triggerClasses="blue-link text-smaller notification-profile mr-1/2"
        class="inline"
      ></profile-popover>
      <div @click="showComment(comment)" class="inline notification-content">
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
        <p class="text-xs text-gray-lightest">
          {{ profilePost.date_created }}
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
  mixins: [view, authorizable],
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