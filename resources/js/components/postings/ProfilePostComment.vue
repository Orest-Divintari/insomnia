<template>
  <div>
    <a @click="showComment(posting.repliable)" class="blue-link">
      <highlight class="text-md" :content="body"></highlight>
    </a>
    <highlight class="italic text-smaller" :content="body"></highlight>
    <div class="flex items-center text-xs text-gray-lightest">
      <profile-popover
        class="pb-1/2"
        :user="posting.poster"
        triggerClasses="text-xs text-gray-lightest underline"
      ></profile-popover>
      <p class="dot"></p>
      <p>Profile post comment</p>
      <p class="dot"></p>
      <p>{{ posting.date_created }}</p>
    </div>
  </div>
</template>

<script>
import highlight from "../Highlight";
import view from "../../mixins/view";
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