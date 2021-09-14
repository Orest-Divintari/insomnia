<template>
  <div v-if="showContent">
    <div :id="typeId" class="reply-container">
      <div class="reply-left-col">
        <profile-popover
          :user="item.poster"
          trigger="avatar"
          triggerClasses="avatar-xl"
        ></profile-popover>
        <profile-popover
          :user="item.poster"
          triggerClasses="mt-1 text-blue-mid text-sm font-bold"
        ></profile-popover>
        <p
          v-if="isThreadReply && isOriginalPoster"
          class="
            bg-green-mid
            rounded
            text-white
            border border-green-900
            px-7
            text-xs
            font-hairline
            mt-1
          "
        >
          Original Poster
        </p>
        <i class="mt-2 fas fa-chevron-down"></i>
      </div>
      <div class="w-full pl-5/2 flex flex-col">
        <div class="flex justify-between items-center">
          <div class="flex">
            <a
              :href="'#' + typeId"
              v-text="item.date_created"
              class="text-xs text-gray-lightest hover:underline font-hairline"
              :class="{ 'mt-2': !isThreadReply }"
            ></a>
          </div>
          <div
            v-if="isThreadReply"
            class="flex items-center text-xs text-gray-lightest"
          >
            <a class="mr-3 fas fa-share-alt"></a>
            <a v-if="signedIn" class="mx-3 far fa-bookmark"></a>
            <div class="bg-blue-reply-border text-white px-5/2 py-2">
              #{{ item.position }}
            </div>
          </div>
        </div>
        <div class="p-5/2 pl-0 h-full" :class="{ 'mt-4': !isThreadReply }">
          <div
            v-if="showIgnoredContent && item.creator_ignored_by_visitor"
            class="pr-5/2 mb-3"
          >
            <p
              class="
                border-l-3 border-blue-mid
                bg-white-catskill
                mb-5/2
                p-3
                pl-0
                text-smaller text-gray-shuttle
              "
            >
              <i class="fas fa-microphone-alt-slash ml-3 text-red-700"></i> You
              are ignoring content by this member.
            </p>
          </div>
          <div v-if="editing">
            <form @submit.prevent="update">
              <input type="text" />
              <wysiwyg
                v-model="body"
                :style-attributes="'reply-form'"
                name="body"
              ></wysiwyg>
              <div class="form-button-container justify-center">
                <button class="form-button mr-3" type="submit">
                  <span class="fas fa-save mr-1"></span> Save
                </button>
                <button class="form-button" @click="cancel" type="submit">
                  Cancel
                </button>
              </div>
            </form>
          </div>
          <div v-else class="flex flex-col">
            <div class="reply-body">
              <highlight :content="body"></highlight>
            </div>
            <div v-if="hasLikes" class="flex pl-1 mb-2">
              <i v-if class="text-blue-like text-sm fas fa-thumbs-up"></i>
              <a href class="text-gray-lightest text-xs underline ml-1"
                >{{ this.likesCount }} likes</a
              >
            </div>
          </div>
        </div>
        <div v-if="signedIn && !editing" class="flex justify-between pb-2">
          <div class="flex">
            <button class="btn-reply-control">
              <i class="fas fa-exclamation-circle"></i>
              Report
            </button>
            <button
              v-if="can('update', item)"
              @click="editing = true"
              class="btn-reply-control"
            >
              <span class="fas fa-pen"></span>
              Edit
            </button>
          </div>
          <div class="flex">
            <like-button
              :path="likePath"
              @liked="updateLikeStatus"
              :item="item"
            ></like-button>
            <quote-reply :reply="item"></quote-reply>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Highlight from "../base/Highlight";
import LikeButton from "../base/LikeButton";
import QuoteReply from "./QuoteReply";
import likeable from "../../mixins/likeable";
import authorizable from "../../mixins/authorizable";

export default {
  components: {
    Highlight,
    LikeButton,
    QuoteReply,
  },
  props: {
    showIgnoredContent: {
      type: Boolean,
      default: false,
    },
    repliable: {
      type: Object,
      default: {},
      required: false,
    },
    item: {
      type: Object,
      default: {},
    },
  },
  mixins: [likeable, authorizable],
  data() {
    return {
      showContent: !this.item.creator_ignored_by_visitor,
      editing: false,
      body: this.item.body,
    };
  },
  computed: {
    likePath() {
      return "/ajax/replies/" + this.item.id + "/likes";
    },
    path() {
      if (this.isThreadReply) {
        return this.threadReplyPath;
      }
      return this.conversationMessagePath;
    },
    threadReplyPath() {
      return "/ajax/replies/" + this.item.id;
    },
    conversationMessagePath() {
      return "/ajax/messages/" + this.item.id;
    },
    isOriginalPoster() {
      return this.item.poster.name == this.repliable.poster.name;
    },
    isThreadReply() {
      return this.item.repliable_type.includes("Thread");
    },
    typeId() {
      if (this.isThreadReply) {
        var type = "post-";
      } else if (this.item.repliable_type.includes("Conversation")) {
        var type = "convMessage-";
      }
      return type + this.item.id;
    },
  },
  watch: {
    showIgnoredContent(newValue, oldValue) {
      this.showContent = newValue;
    },
  },
  methods: {
    update() {
      axios
        .patch(this.path, { body: this.body })
        .then(() => this.hideEdit())
        .catch((error) => showErrorModal(error.response.data));
    },
    hideEdit() {
      this.editing = false;
    },
    cancel() {
      this.editing = false;
      this.body = this.item.body;
    },
  },
};
</script>

<style lang="scss" scoped>
</style>