<template>
  <div>
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
      return "/api/threads/" + this.thread.slug + "/lock";
    },
  },
  methods: {
    lock() {
      axios
        .post(this.path)
        .then((response) => this.onSuccess(true))
        .catch((error) => console.log(error));
    },
    unlock() {
      axios
        .delete(this.path)
        .then((response) => this.onSuccess(false))
        .catch((error) => console.log(error));
    },
    onSuccess(locked) {
      this.locked = locked;
      EventBus.$emit("lock-repliable", locked);
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