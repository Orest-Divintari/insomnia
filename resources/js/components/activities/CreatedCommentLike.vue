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
        liked
        <a @click="showComment(subject.likeable)" class="blue-link">
          {{ subject.likeable.poster.name }}'s comment
        </a>
        on
        <span v-if="ownsPost(subject.likeable.repliable)">your post.</span>
        <span v-else
          >{{ subject.likeable.repliable.poster.name }}'s profile post.</span
        >
      </p>
    </div>
    <div>
      <highlight
        class="italic text-smaller"
        :content="subject.likeable.body"
      ></highlight>
      <p class="text-smaller text-gray-lightest">
        {{ subject.date_created }}
      </p>
    </div>
  </div>
</template>

<script>
import Highlight from "../base/Highlight";
import view from "../../mixins/view";
import authorizable from "../../mixins/authorizable";
export default {
  components: {
    Highlight,
  },
  mixins: [view, authorizable],
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