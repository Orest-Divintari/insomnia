<template>
  <div>
    <new-profile-post
      @added="add"
      :profile-owner="profileOwner"
    ></new-profile-post>
    <div v-if="hasPosts">
      <profile-post
        v-for="(post, index) in items"
        :key="post.id"
        @deleted="remove(index)"
        :item="post"
        :profile-owner="profileOwner"
      ></profile-post>
      <paginator :with-query-string="false" :dataset="dataset"> </paginator>
    </div>
    <p
      v-else
      class="p-7/2 border border-gray-lighter rounded text-black-semi text-sm"
    >
      There are no messages on {{ profileOwner.name }}'s profile yet.
    </p>
  </div>
</template>

<script>
import NewProfilePost from "./NewProfilePost";
import ProfilePost from "./ProfilePost";
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
  data() {
    return {
      items: this.paginatedPosts.data,
      dataset: this.paginatedPosts,
    };
  },
  computed: {
    hasPosts() {
      return this.paginatedPosts.data.length > 0;
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
};
</script>

<style lang="scss" scoped>
</style>