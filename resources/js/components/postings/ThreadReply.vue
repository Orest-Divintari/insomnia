<template>
  <div>
    <a @click="showReply(posting)" class="blue-link">{{ threadTitle }}</a>
    <highlight class="italic text-smaller" :content="body"></highlight>
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
  },
  mixins: [view, postings],
  components: {
    highlight,
  },
  computed: {
    threadTitle() {
      let title = this.posting.repliable.title;
      if (this.query != "") {
        return this.highlightQueryWords(title);
      }
      return title;
    },
    body() {
      let cleanBody = this.clean(this.posting.body);
      if (this.query != "") {
        return this.highlightQueryWords(cleanBody);
      }
      return cleanBody;
    },
  },
  created() {
    this.$emit("getPoster", this.posting.poster);
  },
};
</script>

<style lang="scss" scoped>
</style>