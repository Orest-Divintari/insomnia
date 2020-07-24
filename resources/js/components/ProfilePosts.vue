<template>
  <div>
    <status @added="add" :user="user"></status>
    <profile-post
      v-for="(post, index) in posts"
      :key="post.id"
      @deleted="remove(index)"
      :post="post"
      :user="user"
    ></profile-post>
    <div class="flex justify-end">
      <button v-if="postsExist" class="w-28 btn-white-blue" @click="fetchMore">Older posts</button>
    </div>
  </div>
</template>

<script>
import Status from "../components/Status";
import ProfilePost from "../components/ProfilePost";
export default {
  components: {
    Status,
    ProfilePost
  },
  props: {
    user: {
      type: Object,
      default: {}
    }
  },
  data() {
    return {
      posts: [],
      dataset: {}
    };
  },
  computed: {
    postsExist() {
      return this.dataset.next_page_url != null;
    }
  },
  methods: {
    refresh(data) {
      this.dataset = data;
      this.posts = this.posts.concat(data.data);
    },
    path() {
      return "/api/profiles/" + this.user.name + "/posts";
    },
    fetchMore() {
      axios
        .get(this.dataset.next_page_url)
        .then(({ data }) => this.refresh(data))
        .catch(error => console.log(error));
    },
    fetchData() {
      if (this.posts.length == 0) {
        axios
          .get(this.path())
          .then(({ data }) => this.refresh(data))
          .catch(error => console.log(error));
      }
    },
    add(data) {
      this.posts.unshift(data);
    },
    remove(postIndex) {
      this.posts.splice(postIndex, 1);
    }
  },
  created() {
    this.fetchData();
  }
};
</script>

<style lang="scss" scoped>
</style>