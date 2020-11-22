<template>
  <div>
    <follows
      class="mb-2"
      v-if="hasFollowing"
      :profile-owner="profileOwner"
      :dataset="followsDataset"
    ></follows>
    <followed-by
      v-if="hasFollowers"
      :profile-owner="profileOwner"
      :dataset="followedByDataset"
    ></followed-by>
  </div>
</template>

<script>
import Follows from "./Follows";
import FollowedBy from "./FollowedBy";
import fetch from "../../mixins/fetch";
export default {
  components: {
    Follows,
    FollowedBy,
  },
  props: {
    profileOwner: {
      type: Object,
      default: {},
      required: true,
    },
  },
  mixins: [fetch],
  data() {
    return {
      followsDataset: {},
      followedByDataset: {},
      hasFollowing: false,
      hasFollowers: false,
    };
  },
  computed: {
    path() {
      return "/api/profiles/" + this.profileOwner.name + "/about";
    },
  },
  methods: {
    refresh(data) {
      this.followsDataset = data.follows;
      this.followedByDataset = data.followedBy;
      this.hasFollowing = this.followsDataset.total > 0;
      this.hasFollowers = this.followedByDataset.total > 0;
    },
  },
  created() {
    this.fetchData();
  },
};
</script>

<style lang="scss" scoped>
</style>