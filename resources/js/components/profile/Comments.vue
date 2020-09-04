<template>
  <div class="mt-3">
    <div v-if="commentsExist" class="mb-2 bg-blue-lighter border border-gray-lighter p-2">
      <button
        @click="fetchMore"
        class="text-smaller text-blue-ship-cove font-hairline hover:underline focus:outline-none"
      >View previous comments...</button>
    </div>
    <comment
      @deleted="remove(index)"
      v-for="(comment,index) in comments"
      :key="comment.id"
      :comment="comment"
      :profile-owner="profileOwner"
    ></comment>
    <new-comment @created="add" :profile-post="post"></new-comment>
  </div>
</template>

<script>
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
  data() {
    return {
      comments: [],
      dataset: {},
    };
  },
  computed: {
    commentsExist() {
      return this.dataset.next_page_url != null;
    },
    path() {
      return "/api/posts/" + this.post.id + "/comments";
    },
  },
  methods: {
    add(data) {
      this.comments.push(data);
    },
    remove(index) {
      this.comments.splice(index, 1);
    },
    refresh(paginatedCollection) {
      this.dataset = paginatedCollection;
      this.comments.unshift(...paginatedCollection.data.reverse());
    },
    fetchData() {
      axios
        .get(this.path)
        .then(({ data }) => this.refresh(data))
        .catch((error) => console.log(error));
    },
    fetchMore() {
      axios
        .get(this.dataset.next_page_url)
        .then(({ data }) => this.refresh(data))
        .catch((error) => console.log(error));
    },
  },
  created() {
    this.fetchData();
  },
};
</script>

<style lang="scss" scoped>
</style>