<template>
  <div>
    <a class="text-blue-mid hover:underline" href>{{ data.reply.poster.name}}</a> replied to the thtread -
    <a
      class="text-blue-mid hover:underline"
      href
    >{{ data.reply.thread.title }}</a>.
    <p class="text-xs text-gray-lightest">{{ data.reply.thread.date_updated }}</p>
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