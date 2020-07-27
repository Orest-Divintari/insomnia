<template>
  <div>
    <div :id="'post-'+ reply.id" class="reply-container">
      <div class="reply-left-col">
        <img :src="reply.poster.avatar_path" class="avatar-xl" alt />
        <a
          href
          class="mt-1 text-blue-mid text-sm hover:underline font-bold"
          v-text="reply.poster.name"
        ></a>
        <p
          v-if="reply.poster.name == threadPoster"
          class="bg-green-mid rounded text-white border border-green-900 px-7 text-xs font-hairline"
        >Original Poster</p>
        <i class="mt-2 fas fa-chevron-down"></i>
      </div>
      <div class="w-full">
        <div class="flex justify-between items-center">
          <a
            :href="'#post-'+reply.id"
            v-text="reply.date_created"
            class="text-xs text-gray-lightest hover:underline font-hairline pl-3"
          ></a>
          <div class="flex items-center text-xs text-gray-lightest">
            <a class="mr-3 fas fa-share-alt"></a>
            <a v-if="signedIn" class="mx-3 far fa-bookmark"></a>
            <div class="bg-blue-reply-border text-white px-5/2 py-2">#{{ reply.position }}</div>
          </div>
        </div>
        <div class="p-5/2 h-full">
          <div v-if="editing">
            <form @submit.prevent="update">
              <input type="text" />
              <wysiwyg v-model="body" :style-attributes="'reply-form'" name="body"></wysiwyg>
              <div class="form-button-container justify-center">
                <button class="form-button mr-3" type="submit">
                  <span class="fas fa-save mr-1"></span> Save
                </button>
                <button class="form-button" @click="cancel" type="submit">Cancel</button>
              </div>
            </form>
          </div>
          <div v-else class="flex flex-col h-full pb-8">
            <div class="reply-body">
              <highlight :content="body"></highlight>
            </div>
            <div v-if="hasLikes" class="flex pl-1 mb-2">
              <i v-if class="text-blue-like text-sm fas fa-thumbs-up"></i>
              <a href class="text-gray-lightest text-xs underline ml-1">{{ this.likesCount }} likes</a>
            </div>
            <div v-if="signedIn" class="flex justify-between">
              <div class="flex">
                <button class="btn-reply-control">
                  <i class="fas fa-exclamation-circle"></i>
                  Report
                </button>
                <button
                  v-if="authorize('owns', reply)"
                  @click="editing=true"
                  class="btn-reply-control"
                >
                  <span class="fas fa-pen"></span>
                  Edit
                </button>
              </div>
              <div class="flex">
                <like-button @liked="updateLikeStatus" :item="reply"></like-button>
                <quote-reply :reply="reply"></quote-reply>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Highlight from "../Highlight";
import LikeButton from "../LikeButton";
import QuoteReply from "./QuoteReply";
import replies from "../../mixins/replies";

export default {
  components: {
    Highlight,
    LikeButton,
    QuoteReply,
  },
  props: {
    threadPoster: {
      type: String,
      default: "",
    },
    reply: {
      type: Object,
      default: {},
    },
  },
  mixins: [replies],
  data() {
    return {
      editing: false,
      body: this.reply.body,
      isLiked: this.reply.is_liked,
      likesCount: this.reply.likes_count,
    };
  },
  computed: {
    path() {
      return "/api/replies/" + this.reply.id;
    },
  },
  methods: {
    update() {
      axios
        .patch(this.path, this.data)
        .then(() => this.updated())
        .catch((error) => console.log(error.response));
    },
    cancel() {
      this.editing = false;
      this.body = this.reply.body;
    },
  },
};
</script>

<style lang="scss" scoped>
</style>