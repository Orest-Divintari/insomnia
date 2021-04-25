<template>
  <div>
    <div class="text-md flex">
      <p>
        <profile-popover
          :user="profileOwner"
          class="inline"
          popover-classes="inline"
          triggerClasses="blue-link text-md mr-1/2"
        ></profile-popover>
        commented on
        <a @click="showComment(subject)" class="blue-link">{{
          subject.repliable.poster.name
        }}</a
        >'s profile post.
      </p>
    </div>
    <div>
      <highlight
        class="italic text-smaller"
        :content="subject.body"
      ></highlight>
      <p class="text-smaller text-gray-lightest">
        {{ subject.date_created }}
      </p>
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
  data() {
    return {
      ...this.activity,
    };
  },
};
</script>

<style lang="scss" scoped>
</style>