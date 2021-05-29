<template>
  <div>
    <p :class="textClasses" v-html="value"></p>
  </div>
</template>

<script>
export default {
  props: {
    value: {
      type: String,
      required: true,
    },
    textClasses: {
      type: String,
      required: false,
    },
  },
};
</script>

<style lang="scss" scoped>
</style>