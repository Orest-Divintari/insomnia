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
        <span v-if="belongsToProfile(subject, profileOwner)">
          updated their
          <a @click="showPost(subject)" class="blue-link">status</a>
        </span>
        <span v-else>
          left a message on
          <a
            @click="showPost(subject.profile_owner, subject)"
            class="blue-link"
            >{{ subject.profile_owner.name }}</a
          >'s profile.
        </span>
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
import authorizable from "../../mixins/authorizable";
export default {
  components: {
    highlight,
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
  methods: {
    belongsToProfile(post, user) {
      return post.profile_owner_id == user.id;
    },
  },
};
</script>

<style lang="scss" scoped>
</style>