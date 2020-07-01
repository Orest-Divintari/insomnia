<template>
  <div>
    <a class="text-blue-mid hover:underline" href>{{ data.reply.poster.name }}</a> liked
    <a href class="text-blue-mid hover:underline">your post</a>
    in the thread {{ data.reply.thread.title }}
    <p class="text-xs text-gray-lightest">{{ data.reply.date_updated }}</p>
  </div>
</template>

<script>
export default {
  props: {
    data: {
      type: Object,
      default: {}
    }
  }
};
</script>

<style lang="scss" scoped>
</style>