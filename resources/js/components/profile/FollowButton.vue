<template>
  <div>
    <button @click="toggleFollow" class="btn-white-blue flex items-center">
      <p v-if="isFollowed">Unfollow</p>
      <p v-else>Follow</p>
    </button>
  </div>
</template>

<script>
export default {
  props: {
    followed: {
      required: true,
    },
    profileOwner: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
      isFollowed: this.followed,
    };
  },
  computed: {
    path() {
      return "/api/users/follow/" + this.profileOwner.name;
    },
  },
  watch: {
    followed(newValue, oldValue) {
      this.isFollowed = newValue;
    },
  },
  methods: {
    toggleFollow() {
      if (this.isFollowed) {
        this.unfollow();
      } else {
        this.follow();
      }
    },
    follow() {
      axios
        .post(this.path)
        .then(() => this.refreshFollow())
        .catch((error) => console.log(error.response.error));
    },
    unfollow() {
      axios
        .delete(this.path)
        .then(() => this.refreshFollow())
        .catch((error) => console.log(error.response.error));
    },
    refreshFollow() {
      this.isFollowed = !this.isFollowed;
      this.$emit("follow", this.isFollowed);
    },
  },
};
</script>

<style lang="scss" scoped>
</style>