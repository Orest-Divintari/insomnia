<template>
  <div class="mt-3">
    <div
      v-if="hasMore"
      class="mb-2 bg-blue-lighter border border-gray-lighter p-2"
    >
      <button
        @click="fetchMore"
        class="text-smaller text-blue-ship-cove font-hairline hover:underline focus:outline-none"
      >
        View previous comments...
      </button>
    </div>
    <comment
      @deleted="remove(index)"
      v-for="(comment, index) in items"
      :key="comment.id"
      :item="comment"
      :profile-owner="profileOwner"
    ></comment>
    <new-comment
      :profile-owner="profileOwner"
      @created="add"
      :profile-post="post"
    ></new-comment>
  </div>
</template>

<script>
import fetch from "../../mixins/fetch";
import collection from "../../mixins/collection";
export default {
  props: {
    post: {
      type: Object,
      default: {},
      required: true,
    },
    paginatedComments: {
      type: Object,
    },
    profileOwner: {
      type: Object,
      default: {},
      required: true,
    },
  },
  mixins: [fetch, collection],
  data() {
    return {
      items: [],
      dataset: {},
    };
  },
  computed: {
    path() {
      return "/ajax/posts/" + this.post.id + "/comments";
    },
  },
  methods: {
    refresh(paginatedCollection) {
      this.dataset = paginatedCollection;
      this.items.unshift(...paginatedCollection.data.reverse());
    },
    initialize() {
      if (this.paginatedComments) {
        this.items = this.paginatedComments.data.reverse();
        this.dataset = this.paginatedComments;
      }
    },
  },
  created() {
    this.initialize();
  },
};
</script>

<style lang="scss" scoped>
</style>