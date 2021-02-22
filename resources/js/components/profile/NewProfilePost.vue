<template>
  <div v-if="signedIn">
    <div class="reply-container">
      <div class="reply-left-col w-24">
        <profile-popover
          :user="profileOwner"
          trigger="avatar"
          triggerClasses="avatar-lg"
        ></profile-popover>
      </div>
      <div class="w-full p-3">
        <profile-post-input
          @posted="post"
          :posted="posted"
          v-model="body"
          placeholder="Update your status..."
          button-name="Post"
        ></profile-post-input>
      </div>
    </div>
  </div>
</template>

<script>
import ProfilePostInput from "./ProfilePostInput";
import Wysiwyg from "../Wysiwyg";
export default {
  components: {
    Wysiwyg,
    ProfilePostInput,
  },
  props: {
    profileOwner: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
      body: "",
      isTyping: false,
      posted: false,
    };
  },
  computed: {
    data() {
      return { body: this.body };
    },
    path() {
      return "/ajax/profiles/" + this.profileOwner.name + "/posts";
    },
  },
  methods: {
    refresh(data) {
      this.posted = !this.posted;
      this.$emit("added", data);
    },
    post() {
      axios
        .post(this.path, this.data)
        .then(({ data }) => this.refresh(data))
        .catch((error) => showErrorModal(error.response.data));
    },
  },
};
</script>

<style lang="scss" scoped>
</style>