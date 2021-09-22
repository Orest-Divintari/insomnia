<template>
  <div dusk="lock-thread-button">
    <button @click="toggle" class="btn-white-blue mr-1">{{ title }}</button>
  </div>
</template>

<script>
import EventBus from "../../eventBus";
export default {
  props: {
    thread: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
      locked: this.thread.locked,
    };
  },
  computed: {
    title() {
      if (this.locked) {
        return "Unlock";
      } else {
        return "Lock";
      }
    },
    path() {
      return "/ajax/threads/" + this.thread.slug + "/lock";
    },
  },
  methods: {
    lock() {
      axios
        .patch(this.path)
        .then((response) => this.onSuccess(response.data))
        .catch((error) => console.log(error));
    },
    unlock() {
      axios
        .delete(this.path)
        .then((response) => this.onSuccess(response.data))
        .catch((error) => console.log(error));
    },
    onSuccess(thread) {
      this.locked = thread.locked;
      EventBus.$emit("lock-repliable", thread);
    },
    toggle() {
      if (this.locked) {
        this.unlock();
      } else {
        this.lock();
      }
    },
  },
};
</script>

<style lang="scss" scoped>
</style>