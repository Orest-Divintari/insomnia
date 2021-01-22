<template>
  <div>
    <div
      class="border border-gray-lighter p-4"
      :class="classes(index)"
      v-for="(posting, index) in postings"
      :key="posting.id"
    >
      <div class="flex">
        <profile-popover
          :user="profileOwner"
          trigger="avatar"
          triggerClasses="avatar-lg"
        ></profile-popover>
        <component
          @getPoster="setPoster"
          :posting="posting.subject"
          :is="posting.type"
          class="pl-4"
        ></component>
      </div>
    </div>
    <fetch-more-button
      v-if="itemsExist"
      @fetchMore="fetchMore"
      title="See more"
    ></fetch-more-button>
  </div>
</template>

<script>
import ProfilePostComment from "../postings/ProfilePostComment";
import ProfilePost from "../postings/ProfilePost";
import ThreadReply from "../postings/ThreadReply";
import Thread from "../postings/Thread";
import FetchMoreButton from "./FetchMoreButton";
import fetch from "../../mixins/fetch";
export default {
  components: {
    "created-comment": ProfilePostComment,
    "created-profile-post": ProfilePost,
    "created-reply": ThreadReply,
    "created-thread": Thread,
    FetchMoreButton,
  },
  props: {
    profileOwner: {
      type: Object,
      default: {},
      required: true,
    },
  },
  mixins: [fetch],
  data() {
    return {
      postings: [],
      dataset: [],
      poster: {},
    };
  },
  computed: {
    path() {
      return "/api/profiles/" + this.profileOwner.name + "/postings";
    },
    itemsExist() {
      return this.dataset.next_page_url != null;
    },
  },
  methods: {
    setPoster(poster) {
      this.poster = poster;
    },
    classes(index) {
      return [
        index == 0 ? "rounded rounded-b-none" : "border-t-0 ",
        index == this.dataset.total - 1 ? "rounded rounded-t-none" : "",
      ];
    },
    refresh(paginatedCollection) {
      this.dataset = paginatedCollection;
      this.postings = this.postings.concat(paginatedCollection.data);
    },
  },
  created() {
    this.fetchData();
  },
};
</script>

<style lang="scss" scoped>
</style>