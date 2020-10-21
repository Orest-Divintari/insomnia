<template>
  <div>
    <div
      class="border border-gray-lighter p-4"
      :class="classes(index)"
      v-for="(posting, index) in postings"
      :key="posting.id"
    >
      <div class="flex">
        <img :src="poster.avatar_path" class="avatar-lg" alt />
        <component
          @getPoster="setPoster"
          :posting="posting"
          :is="posting.type"
          :query="query"
          class="pl-4"
        ></component>
      </div>
    </div>
    <fetch-more-button
      v-if="itemsExist"
      @fetchMore="fetchMore"
      name="See more"
    ></fetch-more-button>
  </div>
</template>

<script>
import ProfilePost from "../postings/ProfilePost";
import ProfilePostComment from "../postings/ProfilePostComment";
import ThreadReply from "../postings/ThreadReply";
import Thread from "../postings/Thread";
export default {
  components: {
    ProfilePost,
    ProfilePostComment,
    Thread,
    ThreadReply,
  },
  props: {
    dataset: {
      type: Object,
      required: true,
      default: {},
    },
    query: {
      type: String,
      default: "",
    },
  },

  data() {
    return {
      poster: {},
      postings: this.dataset.data,
    };
  },
  computed: {
    itemsExist() {
      return this.dataset.next_page_url != null;
    },
  },
  methods: {
    setPoster(poster) {
      this.poster = poster;
    },
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
  },
};
</script>

<style lang="scss" scoped>
</style>