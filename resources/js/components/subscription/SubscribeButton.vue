<template>
  <div dusk="subscribe-thread-button">
    <button @click="toggleModals" class="btn-white-blue mr-1">
      <span v-if="subscribed">Unwatch</span>
      <span v-else>Watch</span>
    </button>

    <watch-modal
      @closed="onCloseModal"
      @watch="subscribe"
      :showWatchModal="showWatchModal"
    ></watch-modal>
    <unwatch-modal
      @closed="onCloseModal"
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
    thread: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      subscribed: this.thread.subscribed,
      showWatchModal: false,
      showUnwatchModal: false,
    };
  },
  computed: {
    title() {
      return this.subscribed ? "Unwatch" : "Watch";
    },
    path() {
      return "/ajax/threads/" + this.thread.slug + "/subscriptions";
    },
  },
  methods: {
    onCloseModal() {
      this.showWatchModal = false;
      this.showUnwatchModal = false;
    },
    toggleModals() {
      if (this.subscribed) {
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
          this.onSubscribe();
        })
        .catch((error) => console.log(error));
    },
    unsubscribe() {
      axios
        .delete(this.path)
        .then((response) => this.onUnsubscribe())
        .catch((error) => console.log(error));
    },
    onSubscribe() {
      this.subscribed = true;
    },
    onUnsubscribe() {
      this.subscribed = false;
    },
  },
};
</script>

<style lang="scss" scoped>
</style>