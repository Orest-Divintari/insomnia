<template>
  <div dusk="follows-component">
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
        name="follows-modal "
        :title="'Members ' + profileOwner.name + ' follows'"
        :follow-list="dataset"
      ></follow-list-modal>
    </div>
  </div>
</template>

<script>
import FollowListModal from "./FollowListModal";
import FetchMoreButton from "../profile/FetchMoreButton";
import fetch from "../../mixins/fetch";
import view from "../../mixins/view";
export default {
  components: {
    FetchMoreButton,
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
  computed: {
    hasMore() {
      return this.dataset.next_page_url ? true : false;
    },
  },
  methods: {
    refresh(paginatedCollection) {
      this.dataset = paginatedCollection;
      this.followingUsers = this.followingUsers.concat(
        paginatedCollection.data
      );
    },
  },
};
</script>

<style lang="scss" scoped>
</style>