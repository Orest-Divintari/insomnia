<template>
  <div>
    <a @click="showPost(posting.subject.poster, posting.subject.repliable)" class="blue-link">
      <highlight class="text-md" :content="posting.subject.body"></highlight>
    </a>
    <highlight class="italic text-smaller" :content="posting.subject.body"></highlight>
    <div class="flex items-center text-xs text-gray-lightest">
      <a @click="showProfile(profileOwner)" class="cursor-pointer underline">{{ profileOwner.name }}</a>
      <p class="dot"></p>
      <p>Profile post comment</p>
      <p class="dot"></p>
      <p>{{ posting.subject.date_created }}</p>
    </div>
  </div>
</template>

<script>
import highlight from "../Highlight";
import view from "../../mixins/view";
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