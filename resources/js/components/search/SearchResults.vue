<template>
  <div class="flex flex-col">
    <div
      class="p-4"
      :class="classes(index)"
      v-for="(posting, index) in postings"
      :key="posting.id"
    >
        <component
          :show-ignored-content="showIgnoredContent"
          :posting="posting"
          :is="posting.type"
          :query="query"
        ></component>
    </div>
    <div class="flex justify-between pt-4">
      <div>
        <paginator
          v-if="hasMore"
          :dataset="dataset"
        ></paginator>
      </div>
      <button
          dusk="show-ignored-content-button"
          @click="revealIgnoredContent"
          v-if="dataset.has_ignored_content && !showIgnoredContent"
          class="self-end text-gray-shuttle hover:underline text-smaller"
          >Show ignored content</a
        >
        </button>
    </div>
  </div>
</template>

<script>
import ProfilePost from "../postings/ProfilePost";
import ProfilePostComment from "../postings/ProfilePostComment";
import ThreadReply from "../postings/ThreadReply";
import Thread from "../postings/Thread";
import fetch from "../../mixins/fetch";
export default {
  name: "SearchResults",
  components: {
    ProfilePost,
    ProfilePostComment,
    Thread,
    ThreadReply,
  },
  props: {
    dataset: {
      type: Object,
      required: true,
      default: {},
    },
    query: {
      type: String,
      default: "",
    },
  },
  mixins: [fetch],
  data() {
    return {
      showIgnoredContent: false,
      poster: {},
      postings: this.dataset.data,
    };
  },
  methods: {
    revealIgnoredContent() {
      this.showIgnoredContent = true;
    },
    setPoster(poster) {
      this.poster = poster;
    },
    classes(index) {
      let posting = this.postings[index];
      let postingIsIgnored =
        posting.ignored_by_visitor || posting.creator_ignored_by_visitor;
      return [
        (this.showIgnoredContent && postingIsIgnored) || !postingIsIgnored
          ? "border border-gray-lighter border-b-0"
          : "",
        index == this.dataset.total - 1 ? "border-b" : "",
      ];
    },
    refresh(paginatedCollection) {
      this.dataset = paginatedCollection;
      this.postings = this.postings.concat(paginatedCollection.data);
    },
  },
};
</script>

<style lang="scss" scoped>
</style>