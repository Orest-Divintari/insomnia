<template>
  <div>
    <button @click="toggleModals" class="btn-white-blue mr-1">
      {{ title }}
    </button>

    <watch-modal
      @watch="subscribe"
      :showWatchModal="showWatchModal"
    ></watch-modal>
    <unwatch-modal
      @unwatch="unsubscribe"
      :showUnwatchModal="showUnwatchModal"
    ></unwatch-modal>
  </div>
</template>

<script>
import WatchModal from "./WatchModal";
import UnwatchModal from "./UnwatchModal";

export default {
  components: {
    WatchModal,
    UnwatchModal,
  },
  props: {
    subscriptionStatus: {
      default: false,
    },
    threadSlug: {
      type: String,
      default: "",
    },
  },
  data() {
    return {
      isSubscribed: this.subscriptionStatus == "true" ? true : false,
      thread: this.threadSlug,
      showWatchModal: false,
      showUnwatchModal: false,
    };
  },
  computed: {
    title() {
      return this.isSubscribed ? "Unwatch" : "Watch";
    },
    path() {
      return "/ajax/threads/" + this.thread + "/subscriptions";
    },
  },
  methods: {
    toggleModals() {
      if (this.isSubscribed) {
        this.showUnwatchModal = true;
        this.showWatchModal = false;
        return;
      }
      this.showWatchModal = true;
      this.showUnwatchModal = false;
    },
    subscribe(mailNotifications) {
      axios
        .put(this.path, mailNotifications)
        .then((response) => {
          this.isSubscribed = true;
          console.log(response);
        })
        .catch((error) => console.log(error));
    },
    unsubscribe() {
      axios
        .delete(this.path)
        .then((response) => (this.isSubscribed = false))
        .catch((error) => console.log(error));
    },
  },
};
</script>

<style lang="scss" scoped>
</style>