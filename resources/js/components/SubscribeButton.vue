<template>
  <div>
    <button @click="toggle" class="btn-thread-control mr-1">{{ title }}</button>
  </div>
</template>

<script>
export default {
  props: {
    subscriptionStatus: {
      default: false
    },
    threadSlug: {
      type: String,
      default: ""
    }
  },
  data() {
    return {
      isSubscribed: this.subscriptionStatus == "true" ? true : false,
      thread: this.threadSlug
    };
  },
  computed: {
    title() {
      return this.isSubscribed ? "Unwatch" : "Watch";
    },
    path() {
      return "/api/threads/" + this.thread + "/subscriptions";
    }
  },
  methods: {
    toggle() {
      this.isSubscribed ? this.unsubscribe() : this.subscribe();
    },
    subscribe() {
      axios
        .post(this.path)
        .then(response => (this.isSubscribed = true))
        .catch(error => console.log(error));
    },
    unsubscribe() {
      axios
        .delete(this.path)
        .then(response => (this.isSubscribed = false))
        .catch(error => console.log(error));
    }
  }
};
</script>

<style lang="scss" scoped>
</style>