<template>
  <div dusk="new-comment" v-if="can('post_on_profile', profileOwner)">
    <div>
      <div class="comment-container border-b">
        <div class="comment-avatar">
          <profile-popover
            :user="authUser"
            trigger="avatar"
            triggerClasses="avatar-sm"
          ></profile-popover>
        </div>
        <div class="w-full p-3">
          <input-comment
            @posted="post"
            :posted="posted"
            v-model="body"
            placeholder="Write a comment..."
            button-name="Post comment"
          ></input-comment>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import InputComment from "./InputComment";
import authorizable from "../../mixins/authorizable";
export default {
  components: {
    InputComment,
  },
  props: {
    profilePost: {
      type: Object,
      default: {},
      required: true,
    },
    profileOwner: {
      type: Object,
      default: {},
      required: true,
    },
  },
  mixins: [authorizable],
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
      return "/ajax/posts/" + this.profilePost.id + "/comments";
    },
  },
  methods: {
    post() {
      axios
        .post(this.path, this.data)
        .then(({ data }) => this.onSuccess(data))
        .catch((error) => showErrorModal(error.response.data));
    },
    onSuccess(data) {
      this.posted = !this.posted;
      this.$emit("created", data);
      this.body = "";
    },
  },
};
</script>

<style lang="scss" scoped>
</style>