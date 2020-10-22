<template>
  <div>
    <button @click="toggleFollow" class="btn-white-blue flex items-center">
      {{ follow }}
    </button>
  </div>
</template>

<script>
export default {
  props: {
    profileOwner: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
      isFollowed: this.profileOwner.followed_by_visitor,
    };
  },
  computed: {
    follow() {
      return this.isFollowed ? "unfollow" : "follow";
    },
    path() {
      return "/api/users/follow";
    },
  },
  methods: {
    toggleFollow() {
      axios
        .post(this.path, { username: this.profileOwner.name })
        .then(() => this.refreshFollow())
        .catch((error) => console.log(error.response.error));
    },
    refreshFollow() {
      this.isFollowed = !this.isFollowed;
    },
  },
};
</script>

<style lang="scss" scoped>
</style>