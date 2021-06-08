<template>
  <div
    class="flex notification-item break-words"
    :class="{ 'bg-white-catskill': !read }"
    @mouseenter="hovering = true"
    @mouseleave="hovering = false"
    @click="view"
  >
    <profile-popover
      :user="notification.data.triggerer"
      trigger="avatar"
      triggerClasses="avatar-sm"
    >
    </profile-popover>
    <div class="flex-1 ml-5/2">
      <component
        :is="notification.data.type"
        :notification-data="notification.data"
        :hovering="hovering"
      ></component>
      <div class="flex justify-between">
        <p class="text-xs text-gray-lightest">
          {{ notification.date_created }}
        </p>
        <read-notification-button
          @toggleRead="toggleRead"
          :hovering="hovering"
          :notification="notification"
        >
        </read-notification-button>
      </div>
    </div>
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
    redirect() {
      window.location.href = this.notification.data.redirectTo;
    },
    view() {
      this.markAsRead();
      this.redirect();
    },
  },
};
</script>

<style lang="scss" scoped>
</style>