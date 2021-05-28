<template>
  <div :class="{ '-mt-12': isPaginated }">
    <paginator
      :class="{ 'pb-5': isPaginated }"
      @isPaginated="isPaginated = true"
      :dataset="dataset"
    ></paginator>
    <reply
      v-for="(reply, index) in items"
      :key="reply.id"
      :item="reply"
      :repliable="repliable"
    ></reply>
    <paginator @isPaginated="isPaginated = true" :dataset="dataset"></paginator>
    <new-reply
      :repliable="repliable"
      @created="add"
      v-if="signedIn && !locked"
    ></new-reply>
    <p v-if="!signedIn" class="text-xs mt-4 text-center">
      You must
      <a href="/login" class="text-blue-mid underline">sign in</a> or
      <a href="/register" class="text-blue-mid underline">register</a> to reply
      here.
    </p>
    <div
      v-if="locked"
      class="flex items-center bg-blue-lighter rounded border-l-1 border-blue-mid p-3 text-smaller text-black-semi mt-4"
    >
      <i class="pl-3 fas fa-lock mr-4 text-red-900"></i>
      <p class="pt-1">Closed for new replies</p>
    </div>
  </div>
</template>

<script>
import Reply from "./Reply";
import NewReply from "./NewReply";
import collection from "../../mixins/collection";
import EventBus from "../../eventBus";
export default {
  components: {
    Reply,
    NewReply,
  },
  props: {
    title: {
      type: String,
      default: "",
    },
    repliable: {
      type: Object,
      default: {},
    },
    replies: {
      type: Object,
      default: {},
    },
  },
  mixins: [collection],
  data() {
    return {
      isPaginated: false,
      items: this.replies.data,
      dataset: this.replies,
      locked: this.repliable.locked,
    };
  },
  created() {
    EventBus.$on("lock-repliable", (locked) => {
      this.locked = locked;
    });
  },
};
</script>

<style lang="scss" scoped>
</style>