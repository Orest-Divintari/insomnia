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
      name="Show older posts"
    ></fetch-more-button>
  </div>
</template>

<script>
import NewProfilePost from "./NewProfilePost";
import ProfilePost from "./ProfilePost";
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
  },
  methods: {
    refresh(paginatedCollection) {
      this.dataset = paginatedCollection;
      this.posts = this.posts.concat(paginatedCollection.data);
    },
    path() {
      return "/api/profiles/" + this.profileOwner.name + "/posts";
    },
    fetchMore() {
      axios
        .get(this.dataset.next_page_url)
        .then(({ data }) => this.refresh(data))
        .catch((error) => console.log(error));
    },
    fetchData() {
      axios
        .get(this.path())
        .then(({ data }) => this.refresh(data))
        .catch((error) => console.log(error));
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