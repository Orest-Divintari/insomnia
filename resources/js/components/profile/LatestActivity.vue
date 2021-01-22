<template>
  <div>
    <div
      class="border border-gray-lighter p-4"
      :class="classes(index)"
      v-for="(activity, index) in activities"
      :key="activity.id"
    >
      <div class="flex">
        <profile-popover
          :user="profileOwner"
          trigger="avatar"
          triggerClasses="avatar-lg"
        ></profile-popover>
        <component
          :activity="activity"
          class="pl-4"
          :is="activity.type"
          :profile-owner="profileOwner"
        ></component>
      </div>
    </div>
    <fetch-more-button
      v-if="itemsExist"
      @fetchMore="fetchMore"
      title="Show older items"
    ></fetch-more-button>
  </div>
</template>

<script>
import FetchMoreButton from "./FetchMoreButton";
import CreatedCommentLike from "../activities/CreatedCommentLike";
import CreatedReplyLike from "../activities/CreatedReplyLike";
import CreatedReply from "../activities/CreatedReply";
import CreatedComment from "../activities/CreatedComment";
import CreatedProfilePost from "../activities/CreatedProfilePost";
import CreatedThread from "../activities/CreatedThread";
import fetch from "../../mixins/fetch";
export default {
  components: {
    CreatedCommentLike,
    CreatedReplyLike,
    CreatedReply,
    CreatedComment,
    CreatedProfilePost,
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
  mixins: [fetch],
  data() {
    return {
      activities: [],
      dataset: [],
    };
  },
  computed: {
    path() {
      return "/api/profiles/" + this.profileOwner.name + "/latestActivity";
    },
    itemsExist() {
      return this.dataset.next_page_url != null;
    },
  },
  methods: {
    classes(index) {
      return [
        index % 2 == 1 ? "bg-blue-lighter" : "bg-white",
        index == 0 ? "rounded rounded-b-none" : "border-t-0 ",
        index == this.dataset.total - 1 ? "rounded rounded-t-none" : "",
      ];
    },
    refresh(paginatedCollection) {
      this.dataset = paginatedCollection;
      this.activities = this.activities.concat(paginatedCollection.data);
    },
  },
  created() {
    this.fetchData();
  },
};
</script>

<style lang="scss" scoped>
</style>