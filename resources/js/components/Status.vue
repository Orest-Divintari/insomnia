<template>
  <div v-if="signedIn">
    <div class="reply-container">
      <div class="reply-left-col w-24">
        <img :src="user.avatar_path" class="avatar-lg" alt />
      </div>
      <div class="reply-right-col">
        <profile-post-input
          @posted="post"
          v-model="body"
          placeholder="Update your status"
          button-name="Post"
        ></profile-post-input>
      </div>
    </div>
  </div>
</template>

<script>
import ProfilePostInput from "./ProfilePostInput";
import Wysiwyg from "../components/Wysiwyg";
export default {
  components: {
    Wysiwyg,
    ProfilePostInput,
  },
  props: {
    user: {
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
  methods: {
    data() {
      return { body: this.body };
    },
    newPost(data) {
      this.posted = !this.posted;
      this.$emit("added", data);
    },
    post() {
      let path = "/api/profiles/" + this.user.name + "/posts";
      axios
        .post(path, this.data())
        .then(({ data }) => this.newPost(data))
        .catch((error) => console.log(error));
    },
  },
};
</script>

<style lang="scss" scoped>
</style>