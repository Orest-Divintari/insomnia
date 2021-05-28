<template>
  <div dusk="profile-post-comment" :id="'profile-post-comment-' + item.id">
    <div class="comment-container">
      <div class="comment-avatar">
        <profile-popover
          :user="item.poster"
          trigger="avatar"
          triggerClasses="avatar-sm"
        ></profile-popover>
      </div>
      <div class="post-right-col">
        <div class="post-header mb-0 text-smaller">
          <profile-popover
            :user="item.poster"
            triggerClasses="post-username"
          ></profile-popover>
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

          <div class="flex justify-between">
            <div>
              <button class="btn-reply-control bg-blue-lighter">
                {{ item.date_created }}
              </button>
              <button
                v-if="can('update', item)"
                @click="editing = true"
                class="btn-reply-control bg-blue-lighter"
              >
                Edit
              </button>
              <button
                v-if="can('delete', item)"
                @click="destroy"
                class="btn-reply-control bg-blue-lighter"
              >
                Delete
              </button>
            </div>
            <like-button
              :styleAttributes="'bg-blue-lighter'"
              :path="likePath"
              @liked="updateLikeStatus"
              :item="item"
            ></like-button>
          </div>
          <div v-if="hasLikes" class="flex pl-1 mt-3">
            <i v-if class="text-blue-like text-sm fas fa-thumbs-up"></i>
            <a href class="text-gray-lightest text-xs underline ml-1"
              >{{ this.likesCount }} likes</a
            >
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Highlight from "../Highlight";
import LikeButton from "../LikeButton";
import likeable from "../../mixins/likeable";
import authorizable from "../../mixins/authorizable";
export default {
  components: {
    Highlight,
    LikeButton,
  },
  props: {
    item: {
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
  mixins: [likeable, authorizable],
  data() {
    return {
      body: this.item.body,
      editing: false,
    };
  },
  computed: {
    likePath() {
      return "/ajax/replies/" + this.item.id + "/likes";
    },
    path() {
      return "/ajax/comments/" + this.item.id;
    },
  },
  methods: {
    edit() {
      this.editing = true;
    },
    hideEdit() {
      this.editing = false;
    },
    cancel() {
      this.hideEdit();
      this.body = this.item.body;
    },
    update() {
      axios
        .patch(this.path, { body: this.body })
        .then(() => this.hideEdit())
        .catch((error) => showErrorModal(error.response.data));
    },
    destroy() {
      axios
        .delete(this.path)
        .then(() => this.deleteComment())
        .catch((error) => console.log(error));
    },
    deleteComment() {
      this.$emit("deleted");
    },
  },
};
</script>

<style lang="scss" scoped>
</style>