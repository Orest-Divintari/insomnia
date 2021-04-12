<template>
  <div class="mt-3">
    <div
      v-if="nextPage"
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
      :comment="comment"
      :profile-owner="profileOwner"
    ></comment>
    <new-comment
      @created="add"
      :user="profileOwner"
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
    nextPage() {
      return this.dataset.next_page_url != null;
    },
    path() {
      return "/ajax/posts/" + this.post.id + "/comments";
    },
  },
  methods: {
    refresh(paginatedCollection) {
      this.dataset = paginatedCollection;
      this.items.unshift(...paginatedCollection.data.reverse());
    },
  },
  created() {
    this.fetchData();
  },
};
</script>

<style lang="scss" scoped>
</style>