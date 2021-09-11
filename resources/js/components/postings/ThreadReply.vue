<template>
  <div v-if="showContent">
    <div class="flex">
      <profile-popover
        :user="posting.poster"
        trigger="avatar"
        triggerClasses="avatar-lg"
      >
      </profile-popover>
      <div class="pl-4">
        <a @click="showReply(posting)" class="blue-link">
          {{ highlight(title) }}</a
        >
        <highlight
          class="italic text-smaller"
          :content="highlight(body)"
        ></highlight>
        <div class="flex items-center text-xs text-gray-lightest">
          <profile-popover
            class="pb-1/2"
            :user="posting.poster"
            triggerClasses="text-xs text-gray-lightest underline"
          ></profile-popover>
          <p class="dot"></p>
          <p>Post #{{ posting.position }}</p>
          <p class="dot"></p>
          <p>{{ posting.date_created }}</p>
          <p class="dot"></p>
          <p>
            Category:
            <a
              @click="showCategory(posting.repliable.category)"
              class="cursor-pointer underline"
              >{{ posting.repliable.category.title }}</a
            >
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import view from "../../mixins/view";
import highlight from "../Highlight";
import postings from "../../mixins/postings";
export default {
  props: {
    posting: {
      type: Object,
      default: {},
    },
    query: {
      type: String,
      default: "",
    },
    showIgnoredContent: {
      type: Boolean,
      default: false,
    },
  },
  mixins: [view, postings],
  components: {
    highlight,
  },
  data() {
    return {
      title: this.posting.repliable.title,
      body: this.posting.body,
      showContent: !this.posting.creator_ignored_by_visitor,
    };
  },
  watch: {
    showIgnoredContent(newValue, oldValue) {
      this.showContent = newValue;
    },
  },
  created() {
    this.$emit("getPoster", this.posting.poster);
  },
};
</script>

<style lang="scss" scoped>
</style>