<template>
  <div>
    <button :class="buttonClasses" @click="markAllAsRead">Mark read</button>
  </div>
</template>

<script>
export default {
  props: {
    buttonClasses: {
      type: String,
    },
  },
  methods: {
    markAllAsRead() {
      axios
        .delete("/ajax/notifications/read")
        .then(() => this.$emit("markedAllRead"))
        .catch((error) => console.log(error));
    },
  },
};
</script>

<style lang="scss" scoped>
</style>