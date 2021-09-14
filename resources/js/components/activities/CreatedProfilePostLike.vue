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
        <a @click="showPost(subject.likeable)" class="blue-link"
          >{{ subject.likeable.poster.name }}'s post</a
        >
        on {{ subject.likeable.profile_owner.name }}'s profile.
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
export default {
  components: {
    Highlight,
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