<template>
  <div>
    <div class="border border-gray-lighter p-4 rounded">
      <h1 class="text-md text-black-semi mb-2">Following</h1>
      <div class="flex flex-row">
        <img
          v-for="(user, index) in followingUsers"
          @click="showProfile(user)"
          :src="user.avatar_path"
          class="cursor-pointer avatar-lg mr-3"
          alt
        />
      </div>
      <follow-list-modal
        name="follows-modal "
        :title="'Members ' + profileOwner.name + ' follows'"
        v-if="hasMore"
        :data="dataset"
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
      followingUsers: this.dataset.data,
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