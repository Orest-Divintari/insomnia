<template>
  <div>
    <a @click="showThread(posting)" class="blue-link"
      ><highlight :content="title"></highlight
    ></a>
    <p class="italic text-smaller">
      <highlight :content="body"></highlight>
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
          <p v-for="(tag, index) in posting.tags" :key="index" class="tag ml-0">
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
</template>

<script>
import view from "../../mixins/view";
import highlight from "../Highlight";
import postings from "../../mixins/postings";
export default {
  components: {
    highlight,
  },
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

  computed: {
    title() {
      if (this.query != "") {
        return this.highlightQueryWords(this.posting.title);
      }
      return this.posting.title;
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