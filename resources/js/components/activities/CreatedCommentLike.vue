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
        <a @click="showComment(subject.reply)" class="blue-link">
          {{ subject.reply.poster.name }}
          's comment
        </a>
        on
        <span v-if="ownsPost(subject.reply.repliable)">your post.</span>
        <span v-else
          >{{ subject.reply.repliable.poster.name }}'s profile post.</span
        >
      </p>
    </div>
    <div>
      <highlight
        class="italic text-smaller"
        :content="subject.reply.body"
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
import authorization from "../../mixins/authorization";
export default {
  components: {
    highlight,
  },
  mixins: [view, authorization],
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