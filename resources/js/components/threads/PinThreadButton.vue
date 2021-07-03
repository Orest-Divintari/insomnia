<template>
  <div dusk="pin-thread-button">
    <button @click="toggle" class="btn-white-blue mr-1">{{ title }}</button>
  </div>
</template>

<script>
export default {
  props: {
    thread: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
      pinned: this.thread.pinned,
    };
  },
  computed: {
    title() {
      if (this.pinned) {
        return "Unpin";
      } else {
        return "Pin";
      }
    },
    path() {
      return "/ajax/threads/" + this.thread.slug + "/pin";
    },
  },
  methods: {
    pin() {
      axios
        .patch(this.path)
        .then((response) => this.onSuccess(true))
        .catch((error) => console.log(error));
    },
    unpin() {
      axios
        .delete(this.path)
        .then((response) => this.onSuccess(false))
        .catch((error) => console.log(error));
    },
    onSuccess(pinned) {
      this.pinned = pinned;
    },
    toggle() {
      if (this.pinned) {
        this.unpin();
      } else {
        this.pin();
      }
    },
  },
};
</script>

<style lang="scss" scoped>
</style>