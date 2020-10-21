<template>
  <div>
    <a @click="showPost(posting.profile_owner, posting)" class="blue-link">
      <highlight class="text-md" :content="body"></highlight>
    </a>
    <highlight class="italic text-smaller" :content="body"></highlight>
    <div class="flex items-center text-xs text-gray-lightest">
      <a
        @click="showProfile(posting.poster)"
        class="cursor-pointer underline"
        >{{ posting.poster.name }}</a
      >
      <p class="dot"></p>
      <p>Profile post</p>
      <p class="dot"></p>
      <p>{{ posting.date_created }}</p>
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