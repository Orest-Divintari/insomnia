<template>
  <div>
    <a
      class="blue-link text-md"
      @click="showReply(activity.subject)"
    >{{ activity.subject.repliable.title }}</a>
    <highlight class="italic text-smaller" :content="activity.subject.body"></highlight>
    <div class="flex text-xs text-gray-lightest items-center">
      <p class="text-smaller">{{ activity.subject.date_created }}</p>
      <p class="dot"></p>
      <div class>
        reply by
        <a
          @click="showProfile(activity.subject.poster)"
          class="blue-link"
        >{{ activity.subject.poster.name }}</a>
      </div>
      <p class="dot"></p>
      <a
        @click="showCategory(activity.subject.repliable.category)"
        class="blue-link"
      >{{ activity.subject.repliable.category.title }}</a>
    </div>
  </div>
</template>

<script>
import highlight from "../Highlight";
import view from "../../mixins/view";
export default {
  components: {
    highlight,
  },
  mixins: [view],
  props: {
    activity: {
      type: Object,
      default: {},
    },
    profileOwner: {
      type: Object,
      default: {},
    },
  },
};
</script>

<style lang="scss" scoped>
</style>