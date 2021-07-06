<template>
  <div>
    <profile-post-filters
      class="mb-4"
      :profile-post-filters="profilePostFilters"
    >
    </profile-post-filters>
    <profile-post
      v-if="hasPosts"
      v-for="(post, index) in items"
      :key="post.id"
      @deleted="remove(index)"
      :item="post"
      :profile-owner="post.profile_owner"
      :show-receiver="true"
    ></profile-post>
    <p
      v-else
      class="
        border border-gray-lighter
        p-4
        rounded
        mb-2
        text-black-semi text-sm
      "
    >
      There are not profile posts yet.
    </p>
    <paginator v-if="hasMore" :with-query-string="false" :dataset="dataset">
    </paginator>
  </div>
</template>

<script>
import ProfilePost from "../profile/ProfilePost";
import Paginator from "../Paginator";
import ProfilePostFilters from "./ProfilePostFilters.vue";
export default {
  components: {
    Paginator,
    ProfilePost,
    ProfilePostFilters,
  },
  props: {
    posts: {
      type: Object,
      default: {},
      required: true,
    },
    profilePostFilters: {
      default() {
        return {};
      },
    },
  },
  data() {
    return {
      items: this.posts.data,
      dataset: this.posts,
    };
  },
  computed: {
    hasPosts() {
      return this.dataset.data.length > 0;
    },
    hasMore() {
      return this.dataset.next_page_url ? true : false;
    },
  },
  methods: {
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