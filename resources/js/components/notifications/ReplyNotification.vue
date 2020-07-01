<template>
  <div>
    <a class="text-blue-mid hover:underline" href="google.com">{{ data.reply.poster.name}}</a> replied to the thtread -
    <a
      class="text-blue-mid hover:underline"
      @click="showReply(data.reply)"
    >{{ data.reply.thread.title }}</a>.
    <p class="text-xs text-gray-lightest">{{ data.reply.thread.date_updated }}</p>
  </div>
</template>

<script>
import replies from "../../mixins/replies";
export default {
  props: {
    data: {
      type: Object,
      default: {}
    }
  },
  mixins: [replies]
};
</script>

<style lang="scss" scoped>
</style>