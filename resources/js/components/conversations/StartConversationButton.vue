<template>
  <div dusk="start-conversation-button" v-if="can('start_conversation', user)">
    <button @click="start" class="btn-white-blue">Start conversation</button>
  </div>
</template>
<script>
import authorizable from "../../mixins/authorizable";
export default {
  props: {
    user: {
      type: Object,
      default: {},
      required: true,
    },
  },
  mixins: [authorizable],
  methods: {
    start() {
      window.location.href =
        "/conversations/create?add_participant=" + this.user.name;
    },
  },
};
</script>

<style lang="scss" scoped>
</style>