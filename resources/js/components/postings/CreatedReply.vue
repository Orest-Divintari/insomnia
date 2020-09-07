<template>
  <div>
    <a @click="showReply(posting.subject)" class="blue-link">{{ posting.subject.thread.title }}</a>
    <highlight class="italic text-smaller" :content="posting.subject.body"></highlight>
    <div class="flex items-center text-xs text-gray-lightest">
      <a @click="showProfile(profileOwner)" class="underline">{{ profileOwner.name }}</a>
      <p class="dot"></p>
      <p>Post #{{ posting.subject.position }}</p>
      <p class="dot"></p>
      <p>{{ posting.subject.date_created }}</p>
      <p class="dot"></p>
      <p>
        Category :
        <a
          @click="showCategory(posting.subject.category)"
          class="cursor-pointer underline"
        >{{ posting.subject.thread.category.title }}</a>
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
    profileOwner: {
      type: Object,
      default: {},
    },
  },
  mixins: [view],
  components: {
    highlight,
  },
};
</script>

<style lang="scss" scoped>
</style>