<template>
  <div>
    <a @click="showPost(posting.profile_owner, posting)" class="blue-link">
      <highlight class="text-md" :content="posting.body"></highlight>
    </a>
    <highlight class="italic text-smaller" :content="posting.body"></highlight>
    <div class="flex items-center text-xs text-gray-lightest">
      <a
        @click="showProfile(posting.poster)"
        class="cursor-pointer underline"
      >{{ posting.poster.name }}</a>
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