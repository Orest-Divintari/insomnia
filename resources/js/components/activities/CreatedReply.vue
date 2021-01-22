<template>
  <div>
    <a class="blue-link text-md" @click="showReply(activity.subject)">{{
      activity.subject.repliable.title
    }}</a>
    <highlight class="italic text-smaller" :content="body"></highlight>
    <div class="flex text-xs text-gray-lightest items-center">
      <p class="text-smaller">{{ activity.subject.date_created }}</p>
      <p class="dot"></p>
      <div class>
        reply by
        <profile-popover
          :user="activity.subject.poster"
          class="inline"
          popover-classes="inline"
          triggerClasses="blue-link mr-1/2"
        ></profile-popover>
      </div>
      <p class="dot"></p>
      <a
        @click="showCategory(activity.subject.repliable.category)"
        class="blue-link"
        >{{ activity.subject.repliable.category.title }}</a
      >
    </div>
  </div>
</template>

<script>
import highlight from "../Highlight";
import view from "../../mixins/view";
import postings from "../../mixins/postings";

export default {
  components: {
    highlight,
  },
  mixins: [view, postings],
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
  computed: {
    body() {
      return this.clean(this.activity.subject.body);
    },
  },
};
</script>

<style lang="scss" scoped>
</style>