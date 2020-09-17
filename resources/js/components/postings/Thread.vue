<template>
  <div>
    <a @click="showThread(posting)" class="blue-link">{{ posting.title }}</a>
    <p class="italic text-smaller">
      <highlight :content="posting.body"></highlight>
    </p>
    <div class="flex items-center text-xs text-gray-lightest">
      <a
        @click="showProfile(posting.poster)"
        class="cursor-pointer underline"
      >{{ posting.poster.name }}</a>
      <p class="dot"></p>
      <p>Thread</p>
      <p class="dot"></p>
      <p>{{ posting.date_created }}</p>
      <p class="dot"></p>
      <p>
        Replies:
        {{ posting.replies_count }}
      </p>
      <p class="dot"></p>
      <p>
        Category :
        <a
          @click="showCategory(posting.category)"
          class="cursor-pointer underline"
        >{{ posting.category.title }}</a>
      </p>
    </div>
  </div>
</template>

<script>
import view from "../../mixins/view";
import highlight from "../Highlight";
export default {
  props: {
    posting: {
      type: Object,
      default: {},
    },
  },
  mixins: [view],
  components: {
    highlight,
  },
  created() {
    this.$emit("getPoster", this.posting.poster);
  },
};
</script>

<style lang="scss" scoped>
</style>