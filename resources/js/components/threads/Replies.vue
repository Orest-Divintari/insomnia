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
      :reply="reply"
      :repliable="repliable"
    ></reply>
    <paginator @isPaginated="isPaginated = true" :dataset="dataset"></paginator>
    <new-reply
      :repliable="repliable"
      @created="add"
      v-if="signedIn"
    ></new-reply>
    <p v-else class="text-xs mt-4 text-center">
      You must
      <a href="/login" class="text-blue-mid underline">sign in</a> or
      <a href="/register" class="text-blue-mid underline">register</a> to reply
      here.
    </p>
  </div>
</template>

<script>
import Reply from "./Reply";
import Paginator from "../Paginator";
import NewReply from "./NewReply";
import collection from "../../mixins/collection";
export default {
  components: {
    Reply,
    Paginator,
    NewReply,
  },
  props: {
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
    };
  },
};
</script>

<style lang="scss" scoped>
</style>