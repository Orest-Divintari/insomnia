<template>
  <div v-if="signedIn">
    <div class="reply-container">
      <div class="reply-left-col w-24">
        <img :src="user.avatar_path" class="avatar-lg" alt />
      </div>
      <div class="reply-right-col">
        <profile-post-form v-model="body" placeholder="Update your status" :button-name="Posteee"></profile-post-form>
      </div>
    </div>
  </div>
</template>

<script>
import ProfilePostForm from "./ProfilePostForm";
import Wysiwyg from "../components/Wysiwyg";
export default {
  components: {
    Wysiwyg,
    ProfilePostForm
  },
  props: {
    user: {
      type: Object,
      default: {}
    }
  },
  data() {
    return {
      body: "",
      isTyping: false,
      posted: false
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
        .catch(error => console.log(error));
    }
  }
};
</script>

<style lang="scss" scoped>
</style>