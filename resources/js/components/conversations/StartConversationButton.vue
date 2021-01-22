<template>
  <div>
    <button @click="start" class="btn-white-blue">Start conversation</button>
  </div>
</template>

<script>
export default {
  props: {
    user: {
      type: Object,
      default: {},
      required: true,
    },
  },
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