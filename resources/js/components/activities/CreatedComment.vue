<template>
  <div>
    <div class="text-md flex">
      <p>
        <a @click="showProfile(profileOwner)" class="blue-link">{{ profileOwner.name }}</a>
        commented on
        <a
          @click="showPost(activity.subject.repliable.poster, activity.subject.repliable)"
          class="blue-link"
        >{{ activity.subject.repliable.poster.name }}</a>'s profile post.
      </p>
    </div>
    <div>
      <highlight class="italic text-smaller" :content="activity.subject.body"></highlight>
      <p class="text-smaller text-gray-lightest">{{ activity.subject.date_created }}</p>
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