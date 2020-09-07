<template>
  <div>
    <a @click="showThread(posting.subject)" class="blue-link">{{ posting.subject.title }}</a>
    <p class="italic text-smaller">{{ posting.subject.title }}</p>
    <div class="flex items-center text-xs text-gray-lightest">
      <a @click="showProfile(profileOwner)" class="cursor-pointer underline">{{ profileOwner.name }}</a>
      <p class="dot"></p>
      <p>Thread</p>
      <p class="dot"></p>
      <p>{{ posting.subject.date_created }}</p>
      <p class="dot"></p>
      <p>
        Replies:
        {{ posting.subject.replies_count }}
      </p>
      <p class="dot"></p>
      <p>
        Category :
        <a
          @click="showCategory(posting.subject.category)"
          class="cursor-pointer underline"
        >{{ posting.subject.category.title }}</a>
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