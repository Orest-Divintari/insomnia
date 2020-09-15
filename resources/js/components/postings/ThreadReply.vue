<template>
  <div>
    <a @click="showReply(posting)" class="blue-link">{{ posting.repliable.title }}</a>
    <highlight class="italic text-smaller" :content="posting.body"></highlight>
    <div class="flex items-center text-xs text-gray-lightest">
      <a @click="showProfile(posting.poster)" class="underline">{{ profileOwner.name }}</a>
      <p class="dot"></p>
      <p>Post #{{ posting.position }}</p>
      <p class="dot"></p>
      <p>{{ posting.date_created }}</p>
      <p class="dot"></p>
      <p>
        Category :
        <a
          @click="showCategory(posting.repliable.category)"
          class="cursor-pointer underline"
        >{{ posting.repliable.category.title }}</a>
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