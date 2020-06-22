<template>
  <div>
    <div class="flex justify-end">
      <button class="btn-thread-control mr-1">Ignore</button>
      <button class="btn-thread-control mr-1">Watch</button>
      <button class="btn-thread-control mr-1">Lock</button>
      <button class="btn-thread-control mr-1">Pin</button>
    </div>
    <replies :thread="thread"></replies>
  </div>
</template>

<script>
export default {
  props: {
    thread: {
      type: Object,
      default: {}
    }
  }
};
</script>

<style lang="scss" scoped>
</style>