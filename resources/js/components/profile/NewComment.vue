<template>
  <div>
    <div v-if="signedIn">
      <div class="comment-container border-b">
        <div class="comment-avatar">
          <img :src="user.avatar_path" class="avatar-sm" alt />
        </div>
        <div class="w-full p-3">
          <profile-post-input
            @posted="post"
            :posted="posted"
            v-model="body"
            placeholder="Write a comment..."
            button-name="Post comment"
          ></profile-post-input>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ProfilePostInput from "./ProfilePostInput";
export default {
  components: {
    ProfilePostInput,
  },
  props: {
    profilePost: {
      type: Object,
      default: {},
      required: true,
    },
  },
  data() {
    return {
      body: "",
      posted: false,
    };
  },
  computed: {
    data() {
      return { body: this.body };
    },
    path() {
      return "/api/posts/" + this.profilePost.id + "/comments";
    },
  },
  methods: {
    post() {
      axios
        .post(this.path, this.data)
        .then(({ data }) => this.refresh(data))
        .catch((error) => console.log(error));
    },
    refresh(data) {
      this.posted = !this.posted;
      this.$emit("created", data);
      this.body = "";
    },
  },
};
</script>

<style lang="scss" scoped>
</style>