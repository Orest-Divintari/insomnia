<template>
  <div>
    <div
      class="border border-gray-lighter p-4"
      :class="classes(index)"
      v-for="(posting, index) in postings"
      :key="posting.id"
    >
      <div class="flex">
        <img :src="profileOwner.avatar_path" class="avatar-lg" alt />
        <component :posting="posting" :is="posting.type" class="pl-4" :profile-owner="profileOwner"></component>
      </div>
    </div>
    <fetch-more-button v-if="itemsExist" @fetchMore="fetchMore" name="See more"></fetch-more-button>
  </div>
</template>

<script>
import CreatedComment from "../postings/CreatedComment";
import CreatedProfilePost from "../postings/CreatedProfilePost";
import CreatedReply from "../postings/CreatedReply";
import CreatedThread from "../postings/CreatedThread";
import FetchMoreButton from "./FetchMoreButton";
export default {
  components: {
    CreatedComment,
    CreatedProfilePost,
    CreatedReply,
    CreatedThread,
    FetchMoreButton,
  },
  props: {
    profileOwner: {
      type: Object,
      default: {},
      required: true,
    },
  },
  data() {
    return {
      postings: [],
      dataset: [],
    };
  },
  computed: {
    path() {
      return "/api/profiles/" + this.profileOwner.name + "/latestActivity/true";
    },
    itemsExist() {
      return this.dataset.next_page_url != null;
    },
  },
  methods: {
    classes(index) {
      return [
        index == 0 ? "rounded rounded-b-none" : "border-t-0 ",
        index == this.dataset.total - 1 ? "rounded rounded-t-none" : "",
      ];
    },
    refresh(paginatedCollection) {
      this.dataset = paginatedCollection;
      this.postings = this.postings.concat(paginatedCollection.data);
    },
    fetchMore() {
      axios
        .get(this.dataset.next_page_url)
        .then(({ data }) => this.refresh(data))
        .catch((error) => console.log(error));
    },
    fetchData() {
      axios
        .get(this.path)
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