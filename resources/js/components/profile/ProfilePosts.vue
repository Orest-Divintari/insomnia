<template>
  <div>
    <new-profile-post
      @added="add"
      :profile-owner="profileOwner"
    ></new-profile-post>
    <profile-post
      v-for="(post, index) in posts"
      :key="post.id"
      @deleted="remove(index)"
      :post="post"
      :profile-owner="profileOwner"
    ></profile-post>
    <fetch-more-button
      v-if="itemsExist"
      @fetchMore="fetchMore"
      title="Show older posts"
    ></fetch-more-button>
  </div>
</template>

<script>
import NewProfilePost from "./NewProfilePost";
import ProfilePost from "./ProfilePost";
import fetch from "../../mixins/fetch";
export default {
  components: {
    NewProfilePost,
    ProfilePost,
  },
  props: {
    profileOwner: {
      type: Object,
      default: {},
    },
  },
  mixins: [fetch],
  data() {
    return {
      posts: [],
      dataset: {},
    };
  },
  computed: {
    itemsExist() {
      return this.dataset.next_page_url != null;
    },
    path() {
      return "/ajax/profiles/" + this.profileOwner.name + "/posts";
    },
  },
  methods: {
    refresh(paginatedCollection) {
      this.dataset = paginatedCollection;
      this.posts = this.posts.concat(paginatedCollection.data);
    },
    add(data) {
      this.posts.unshift(data);
    },
    remove(index) {
      this.posts.splice(index, 1);
    },
  },
  created() {
    this.fetchData();
  },
};
</script>

<style lang="scss" scoped>
</style>