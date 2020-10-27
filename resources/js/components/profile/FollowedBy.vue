<template>
  <div>
    <div class="border border-gray-lighter p-4 rounded">
      <h1 class="text-md text-black-semi mb-2">Followers</h1>
      <div class="flex flex-row">
        <a
          v-for="(user, index) in followedByUsers"
          :href="/profiles/ + user.name"
          ><img :src="user.avatar_path" class="avatar-lg mr-3" alt />
        </a>
      </div>
      <follow-list-modal
        name="followedBy-modal"
        :title="'Members following ' + profileOwner.name"
        v-if="hasMore"
        :data="dataset"
      ></follow-list-modal>
    </div>
  </div>
</template>

<script>
import FollowListModal from "./FollowListModal";
import FetchMoreButton from "../profile/FetchMoreButton";
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
  data() {
    return {
      followedByUsers: this.dataset.data,
    };
  },
  computed: {
    hasMore() {
      return this.dataset.next_page_url ? true : false;
    },
  },
  methods: {
    fetchMore() {
      axios
        .get(this.dataset.next_page_url)
        .then(({ data }) => this.refresh(data))
        .catch((error) => console.log(error));
    },
    refresh(paginatedCollection) {
      this.dataset = paginatedCollection;
      this.followedByUsers = this.followedByUsers.concat(
        paginatedCollection.data
      );
    },
  },
};
</script>

<style lang="scss" scoped>
</style>