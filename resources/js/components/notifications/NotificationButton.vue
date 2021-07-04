<template>
  <div @click="fetchData">
    <dropdown styleClasses="w-80 border-r-0 border-l-0">
      <template v-slot:dropdown-trigger>
        <div class="relative h-14 text-center pt-4 px-2">
          <i class="fas fa-bell"></i>
          <i
            v-if="hasUnviewedNotifications"
            class="notification-badge"
            v-text="unviewedCount"
          ></i>
        </div>
      </template>
      <template v-slot:dropdown-items>
        <div class="dropdown-title">Notifications</div>
        <div v-if="fetchedData">
          <div
            style="max-height: 18rem"
            class="overflow-y-scroll overflow-x-hidden"
            v-if="notificationsExist"
          >
            <div v-for="(notification, index) in notifications">
              <notification
                :read-all="readAll"
                :notification="notification"
              ></notification>
            </div>
          </div>
          <p v-else class="notification-item">
            You do not have any recent notifications.
          </p>
        </div>
        <div v-else class="notification-item">...</div>
        <div class="dropdown-footer-item flex items-center shadow-2xl">
          <div v-if="notificationsExist" class="flex items-center">
            <a href="/account/notifications" class="blue-link">Show all</a>
            <p class="dot"></p>
          </div>
          <div v-if="notificationsExist" class="flex items-center">
            <read-all-notifications-button
              @markedAllRead="onMarkedAllRead"
              button-classes="blue-link active:text-blue-mid-light focus:outline-none"
            >
            </read-all-notifications-button>
            <p class="dot"></p>
          </div>
          <a href="/account/preferences" class="blue-link">Preferences</a>
        </div>
      </template>
    </dropdown>
  </div>
</template>

<script>
import store from "../../store";
import Notifications from "../account/Notifications";
import ReadAllNotificationsButton from "../notifications/ReadAllNotificationsButton.vue";
export default {
  components: {
    Notifications,
    ReadAllNotificationsButton,
  },
  data() {
    return {
      state: store.state,
      readAll: false,
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
    hasUnviewedNotifications() {
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
        .then((data) => this.refresh(data))
        .catch((error) => console.log(error));
    },
    refresh(data) {
      this.notifications = data.data;
      this.fetchedData = true;
    },
    onMarkedAllRead() {
      this.readAll = true;
    },
  },
};
</script>

<style lang="scss" scoped>
</style>