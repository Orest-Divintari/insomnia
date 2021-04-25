<template>
  <div>
    <new-profile-post
      @added="add"
      :profile-owner="profileOwner"
    ></new-profile-post>
    <profile-post
      v-for="(post, index) in items"
      :key="post.id"
      @deleted="remove(index)"
      :post="post"
      :profile-owner="profileOwner"
    ></profile-post>
    <paginator :dataset="dataset"> </paginator>
  </div>
</template>

<script>
import NewProfilePost from "./NewProfilePost";
import ProfilePost from "./ProfilePost";
import fetch from "../../mixins/fetch";
import FetchMoreButton from "./FetchMoreButton";
import Paginator from "../Paginator";
export default {
  components: {
    Paginator,
    FetchMoreButton,
    NewProfilePost,
    ProfilePost,
  },
  props: {
    profileOwner: {
      type: Object,
      default: {},
    },
    paginatedPosts: {
      type: Object,
      default: {},
    },
  },
  mixins: [fetch],
  data() {
    return {
      items: this.paginatedPosts.data,
      dataset: this.paginatedPosts,
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
      this.items = this.items.concat(paginatedCollection.data);
    },
    add(data) {
      this.items.unshift(data);
    },
    remove(index) {
      this.items.splice(index, 1);
    },
  },
  created() {
    // this.fetchData();
  },
};
</script>

<style lang="scss" scoped>
</style>