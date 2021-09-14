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
        <a @click="showThread(posting)" class="blue-link"
          ><highlight :content="highlight(title)"></highlight
        ></a>
        <p class="italic text-smaller">
          <highlight :content="highlight(body)"></highlight>
        </p>
        <div class="flex items-center text-xs text-gray-lightest">
          <profile-popover
            class="pb-1/2"
            :user="posting.poster"
            triggerClasses="text-xs text-gray-lightest underline"
          ></profile-popover>
          <p class="dot"></p>
          <p>Thread</p>
          <p class="dot"></p>
          <p>{{ posting.date_created }}</p>
          <div
            v-if="posting.tags && posting.tags.length > 0"
            class="flex items-center"
          >
            <p class="dot"></p>
            <div class="flex items-center">
              <p
                v-for="(tag, index) in posting.tags"
                :key="index"
                class="tag ml-0"
              >
                {{ tag.name }}
              </p>
            </div>
          </div>
          <p class="dot"></p>
          <p>
            Replies:
            {{ posting.replies_count }}
          </p>
          <p class="dot"></p>
          <p>
            Category:
            <a
              @click="showCategory(posting.category)"
              class="cursor-pointer underline"
              >{{ posting.category.title }}</a
            >
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import view from "../../mixins/view";
import Highlight from "../base/Highlight";
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
      title: this.posting.title,
      body: this.posting.body,
      showContent: false,
    };
  },
  watch: {
    showIgnoredContent(newValue, oldValue) {
      this.showContent = newValue;
    },
  },
  methods: {
    isIgnored() {
      return (
        this.posting.creator_ignored_by_visitor ||
        this.posting.ignored_by_visitor
      );
    },
  },
  created() {
    this.$emit("getPoster", this.posting.poster);
    this.showContent = !this.isIgnored();
  },
};
</script>

<style lang="scss" scoped>
</style>