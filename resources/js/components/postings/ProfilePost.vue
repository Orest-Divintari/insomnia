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
        <a @click="showPost(posting)" class="blue-link">
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
          <p>Profile post</p>
          <p class="dot"></p>
          <p>{{ posting.date_created }}</p>
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
      body: this.posting.body,
      showContent: !this.posting.creator_ignored_by_visitor,
    };
  },
  watch: {
    showIgnoredContent(newValue, oldValue) {
      this.showContent = newValue;
    },
  },
};
</script>

<style lang="scss" scoped>
</style>