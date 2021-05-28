<template>
  <div dusk="profile-post" :id="'profile-post-' + item.id">
    <div class="reply-container">
      <div class="reply-left-col w-24">
        <profile-popover
          :user="item.poster"
          trigger="avatar"
          triggerClasses="avatar-lg"
        ></profile-popover>
      </div>
      <div class="post-right-col">
        <div class="post-header">
          <profile-popover
            :user="item.poster"
            triggerClasses="post-username"
          ></profile-popover>
          <p class="dot"></p>
          <p class="text-sm text-gray-lightest" v-text="item.date_created"></p>
        </div>
        <div v-if="editing">
          <wysiwyg v-model="body" name="body"></wysiwyg>
          <div class="mt-5">
            <button @click="update" class="form-button mr-3">
              <span class="fas fa-save mr-1"></span> Save
            </button>
            <button class="form-button" @click="cancel" type="submit">
              Cancel
            </button>
          </div>
        </div>
        <div v-else>
          <div class="reply-body">
            <highlight :content="body"></highlight>
          </div>
          <div v-if="hasLikes" class="flex pl-1 mb-2">
            <i v-if class="text-blue-like text-sm fas fa-thumbs-up"></i>
            <a href class="text-gray-lightest text-xs underline ml-1"
              >{{ this.likesCount }} likes</a
            >
          </div>
          <div class="flex justify-between">
            <div>
              <button
                v-if="can('update', item)"
                @click="editing = true"
                class="btn-reply-control"
              >
                Edit
              </button>
              <button
                v-if="can('delete', item)"
                @click="destroy"
                class="btn-reply-control"
              >
                Delete
              </button>
            </div>
            <like-button
              :path="likePath"
              @liked="updateLikeStatus"
              :item="item"
            ></like-button>
          </div>
        </div>
        <comments
          :profile-owner="profileOwner"
          :paginated-comments="item.paginatedComments"
          :post="item"
        ></comments>
      </div>
    </div>
  </div>
</template>

<script>
import Highlight from "../Highlight";
import Wysiwyg from "../Wysiwyg";
import Comments from "./Comments";
import LikeButton from "../LikeButton";
import likeable from "../../mixins/likeable";
import authorizable from "../../mixins/authorizable";
export default {
  components: {
    Highlight,
    Comments,
    LikeButton,
  },
  props: {
    item: {
      type: Object,
      default: {},
    },
    profileOwner: {
      type: Object,
      default: {},
    },
  },
  mixins: [likeable, authorizable],
  data() {
    return {
      body: this.item.body,
      editing: false,
    };
  },
  computed: {
    likePath() {
      return "/ajax/profile-posts/" + this.item.id + "/likes";
    },
  },
  methods: {
    cancel() {
      this.editing = false;
    },
    data() {
      return { body: this.body };
    },
    path() {
      return "/ajax/profile/posts/" + this.item.id;
    },
    update() {
      axios
        .patch(this.path(), this.data())
        .then((response) => console.log(response))
        .catch((error) => showErrorModal(error.response.data));

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