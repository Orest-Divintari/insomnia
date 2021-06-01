<template>
  <div @click="fetchData">
    <dropdown styleClasses="w-80">
      <template v-slot:dropdown-trigger>
        <div class="relative hover:bg-blue-mid h-14 text-center pt-4 px-2">
          <i class="fas fa-bell"></i>
          <i
            v-if="unviewedNotifications"
            class="notification-badge"
            v-text="unviewedCount"
          ></i>
        </div>
      </template>
      <template v-slot:dropdown-items>
        <div class="dropdown-title">Notifications</div>
        <div v-if="fetchedData">
          <div v-if="notificationsExist">
            <div
              @click="markAsRead(notification.id)"
              v-for="(notification, index) in notifications"
              :key="notification.id"
              class="notification-item"
            >
              <component
                :is="notification.data.type"
                :notification-data="notification.data"
              ></component>
            </div>
          </div>
          <div v-if="!notificationsExist">
            <div class="notification-item">You have no new notifications</div>
          </div>
        </div>
        <div v-else class="notification-item">...</div>
      </template>
    </dropdown>
  </div>
</template>

<script>
import store from "../../store";
export default {
  data() {
    return {
      state: store.state,
      notifications: [],
      fetchedData: false,
    };
  },
  computed: {
    path() {
      return "/ajax/notifications";
    },
    unviewedCount() {
      return this.state.visitor.unviewed_notifications_count;
    },
    unviewedNotifications() {
      return this.unviewedCount > 0;
    },
    notificationsExist() {
      return this.notifications.length > 0;
    },
  },
  methods: {
    fetchData() {
      axios
        .get(this.path)
        .then(({ data }) => this.refresh(data))
        .catch((error) => console.log(error));
    },
    readPath(notificationId) {
      return "/ajax/notifications/" + notificationId;
    },
    refresh(data) {
      this.notifications = data;
      this.fetchedData = true;
    },
    markAsRead(notificationId) {
      axios
        .delete(this.readPath(notificationId))
        .then((response) => notification.length)
        .catch((error) => console.log(error));
    },
  },
};
</script>

<style lang="scss" scoped>
</style>