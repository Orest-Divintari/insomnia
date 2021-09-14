<template>
  <div>
    <a class="blue-link text-md" @click="showReply(subject)">{{
      subject.repliable.title
    }}</a>
    <highlight class="italic text-smaller" :content="body"></highlight>
    <div class="flex text-xs text-gray-lightest items-center">
      <p class="text-smaller">{{ subject.date_created }}</p>
      <p class="dot"></p>
      <div class>
        reply by
        <profile-popover
          :user="subject.poster"
          class="inline"
          popover-classes="inline"
          triggerClasses="blue-link mr-1/2"
        ></profile-popover>
      </div>
      <p class="dot"></p>
      <a @click="showCategory(subject.repliable.category)" class="blue-link">{{
        subject.repliable.category.title
      }}</a>
    </div>
  </div>
</template>

<script>
import Highlight from "../base/Highlight";
import view from "../../mixins/view";
import postings from "../../mixins/postings";

export default {
  components: {
    Highlight,
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
      return this.clean(this.subject.body);
    },
  },
  data() {
    return {
      ...this.activity,
    };
  },
};
</script>

<style lang="scss" scoped>
</style>