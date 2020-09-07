<template>
  <div>
    <div class="comment-container">
      <div class="comment-avatar">
        <img :src="comment.poster.avatar_path
        " class="avatar-sm" alt />
      </div>
      <div class="post-right-col">
        <div class="post-header mb-0 text-smaller">
          <a
            :href="'/profiles/' + comment.poster.name"
            class="post-username"
            v-text="comment.poster.name"
          ></a>
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

          <div class="flex justify-between">
            <div>
              <button class="btn-reply-control bg-blue-lighter">{{ comment.date_created }}</button>
              <button
                v-if="authorize('owns', comment)"
                @click="editing=true"
                class="btn-reply-control bg-blue-lighter"
              >Edit</button>
              <button
                v-if="authorize('owns', comment) || authorize('is', profileOwner)"
                @click="destroy"
                class="btn-reply-control bg-blue-lighter"
              >Delete</button>
            </div>
            <like-button
              v-if="!authorize('owns', comment)"
              :styleAttributes="'bg-blue-lighter'"
              @liked="updateLikeStatus"
              :item="comment"
            ></like-button>
          </div>
          <div v-if="hasLikes" class="flex pl-1 mt-3">
            <i v-if class="text-blue-like text-sm fas fa-thumbs-up"></i>
            <a href class="text-gray-lightest text-xs underline ml-1">{{ this.likesCount }} likes</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Highlight from "../Highlight";
import LikeButton from "../LikeButton";
import replies from "../../mixins/replies";
export default {
  components: {
    Highlight,
    LikeButton,
  },
  props: {
    comment: {
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
  mixins: [replies],
  data() {
    return {
      body: this.comment.body,
      editing: false,
      isLiked: this.comment.is_liked,
      likesCount: this.comment.likes_count,
    };
  },
  computed: {
    path() {
      return "/api/comments/" + this.comment.id;
    },
  },
  methods: {
    cancel() {
      this.editing = false;
      this.body = this.comment.body;
    },
    update() {
      axios
        .patch(this.path, this.data)
        .then(() => this.updated())
        .catch((error) => console.log(error));
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