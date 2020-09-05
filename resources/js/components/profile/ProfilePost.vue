<template>
  <div :id="'#profile-post-' + post.id">
    <div class="reply-container">
      <div class="reply-left-col w-24">
        <img :src="post.poster.avatar_path
        " class="avatar-lg" alt />
      </div>
      <div class="post-right-col">
        <div class="post-header">
          <a
            :href="'/profiles/' + post.poster.name"
            class="post-username"
            v-text="post.poster.name"
          ></a>
          <p class="dot"></p>
          <p class="text-sm text-gray-lightest" v-text="post.date_created"></p>
        </div>
        <div v-if="editing">
          <wysiwyg v-model="body" name="body"></wysiwyg>
          <div class="mt-5">
            <button @click="update" class="form-button mr-3">
              <span class="fas fa-save mr-1"></span> Save
            </button>
            <button class="form-button" @click="cancel" type="submit">Cancel</button>
          </div>
        </div>
        <div v-else>
          <div class="reply-body">
            <highlight :content="body"></highlight>
          </div>
          <div class="flex" v-if="authorize('owns', post)">
            <button @click="editing=true" class="btn-reply-control">Edit</button>
            <button @click="destroy" class="btn-reply-control">Delete</button>
          </div>
        </div>
        <comments :profile-owner="profileOwner" :post="post"></comments>
      </div>
    </div>
  </div>
</template>

<script>
import Highlight from "../Highlight";
import Wysiwyg from "../Wysiwyg";
import Comments from "./Comments";
export default {
  components: {
    Highlight,
    Comments,
  },
  props: {
    post: {
      type: Object,
      default: {},
    },
    profileOwner: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
      body: this.post.body,
      editing: false,
    };
  },
  methods: {
    cancel() {
      this.editing = false;
    },
    data() {
      return { body: this.body };
    },
    path() {
      return "/api/profile/posts/" + this.post.id;
    },
    update() {
      axios
        .patch(this.path(), this.data())
        .then((response) => console.log(response))
        .catch((error) => console.log(error));

      this.cancel();
    },
    destroy() {
      axios
        .delete(this.path())
        .then((response) => this.deletePost())
        .catch((error) => console.log(error));
    },
    deletePost() {
      this.$emit("deleted");
    },
  },
};
</script>

<style lang="scss" scoped>
</style>