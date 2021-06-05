<template>
  <div
    class="flex justify-between"
    @mouseleave="hovering = false"
    @mouseenter="hovering = true"
    @click="markAsRead"
  >
    <component
      :is="notification.data.type"
      :notification-data="notification.data"
    ></component>
    <read-notification-button
      @toggleRead="toggleRead"
      :hovering="hovering"
      class="-ml-5 self-end"
      :notification="notification"
    >
    </read-notification-button>
  </div>
</template>

<script>
import ReadNotificationButton from "./ReadNotificationButton";
export default {
  components: {
    ReadNotificationButton,
  },
  props: {
    notification: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      hovering: false,
      read: this.notification.is_read,
    };
  },
  computed: {
    path() {
      return "/ajax/notifications/" + this.notification.id + "/read";
    },
  },
  methods: {
    toggleRead() {
      if (this.read) {
        this.read = false;
        this.markAsUnread();
      } else {
        this.read = true;
        this.markAsRead();
      }
    },
    markAsRead() {
      axios.patch(this.path).catch((error) => console.log(error));
    },
    markAsUnread() {
      axios.delete(this.path).catch((error) => console.log(error));
    },
  },
};
</script>

<style lang="scss" scoped>
</style>