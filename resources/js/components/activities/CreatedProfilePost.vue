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
        <span v-if="ownsPost(activity.subject)">
          updated their
          <a @click="showPost(profileOwner, activity.subject)" class="blue-link"
            >status</a
          >
        </span>
        <span v-else>
          left a message on
          <a
            @click="showPost(activity.subject.poster, activity.subject)"
            class="blue-link"
            >{{ activity.subject.poster.name }}</a
          >'s profile.
        </span>
      </p>
    </div>
    <div>
      <highlight
        class="italic text-smaller"
        :content="activity.subject.body"
      ></highlight>
      <p class="text-smaller text-gray-lightest">
        {{ activity.subject.date_created }}
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
};
</script>

<style lang="scss" scoped>
</style>