<template>
  <div dusk="followings-component">
    <div class="border border-gray-lighter p-4 rounded">
      <h1 class="text-md text-black-semi mb-2">Following</h1>
      <div class="flex flex-row">
        <profile-popover
          class="mr-3"
          :user="user"
          v-for="(user, index) in followingUsers"
          trigger="avatar"
          triggerClasses="avatar-lg"
          :key="index"
        ></profile-popover>
      </div>
      <follow-list-modal
        v-if="hasMore"
        name="followings-modal "
        :title="'Members ' + profileOwner.name + ' follows'"
        :follow-list="dataset"
      ></follow-list-modal>
    </div>
  </div>
</template>

<script>
import FollowListModal from "./FollowListModal";
import fetch from "../../mixins/fetch";
import view from "../../mixins/view";
export default {
  components: {
    FollowListModal,
  },
  props: {
    profileOwner: {
      type: Object,
      default: {},
    },
    dataset: {
      type: Object,
      default: {},
    },
  },
  mixins: [fetch, view],
  data() {
    return {
      followingUsers: [...this.dataset.data],
    };
  },
};
</script>

<style lang="scss" scoped>
</style>