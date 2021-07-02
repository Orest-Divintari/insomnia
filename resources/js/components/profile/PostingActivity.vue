<template>
  <div dusk="postings-tab">
    <div v-if="hasPostings">
      <div
        class="border border-gray-lighter p-4"
        :class="classes(index)"
        v-for="(posting, index) in postings"
        :key="posting.id"
      >
        <component
          @getPoster="setPoster"
          :posting="posting.subject"
          :is="posting.type"
        ></component>
      </div>
      <fetch-more-button
        v-if="hasMore"
        @fetchMore="fetchMore"
        title="See more"
      ></fetch-more-button>
    </div>
    <p
      v-if="!hasPostings && fetchedData"
      class="
        border border-gray-lighter
        p-4
        rounded
        mb-2
        text-black-semi text-sm
      "
    >
      {{ profileOwner.name }} has not posted any content recently.
    </p>
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
      fetchedData: false,
      postings: [],
      dataset: [],
      poster: {},
    };
  },
  computed: {
    path() {
      return "/ajax/profiles/" + this.profileOwner.name + "/postings";
    },
    hasPostings() {
      return this.postings.length > 0;
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