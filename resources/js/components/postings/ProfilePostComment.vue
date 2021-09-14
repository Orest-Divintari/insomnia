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
        <a @click="showComment(posting.repliable)" class="blue-link">
          <highlight class="text-md" :content="highlight(body)"></highlight>
        </a>
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
          <p>Profile post comment</p>
          <p class="dot"></p>
          <p>{{ posting.date_created }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Highlight from "../base/Highlight";
import view from "../../mixins/view";
import postings from "../../mixins/postings";
export default {
  components: {
    Highlight,
  },
  props: {
    posting: {
      type: Object,
      default: {},
    },
    showIgnoredContent: {
      type: Boolean,
      default: false,
    },
    query: {
      type: String,
      default: "",
    },
  },
  mixins: [view, postings],
  data() {
    return {
      body: this.posting.body,
      showContent: !this.posting.creator_ignored_by_visitor,
    };
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