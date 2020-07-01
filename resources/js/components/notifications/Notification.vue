<template>
  <div>
    <dropdown styleClasses="w-80">
      <template v-slot:dropdown-trigger>
        <i class="fas fa-bell"></i>
      </template>
      <template v-slot:dropdown-items>
        <div class="dropdown-title">Alerts</div>
        <div v-if="notifications.length > 0">
          <div
            @click="markAsRead(notification.id)"
            v-for="(notification, index) in notifications"
            :key="notification.id"
            class="dropdown-item"
          >
            <reply-notification v-if="isReply(notification)" :data="notification.data"></reply-notification>
            <like-notification v-else :data="notification.data"></like-notification>
          </div>
        </div>
        <div v-else>
          <div class="dropdown-item">You have no new alerts</div>
        </div>
      </template>
    </dropdown>
  </div>
</template>

<script>
import ReplyNotification from "./ReplyNotification";
import LikeNotification from "./LikeNotification";
export default {
  components: {
    ReplyNotification,
    LikeNotification
  },
  data() {
    return {
      notifications: []
    };
  },
  computed: {
    indexPath() {
      return "/api/notifications";
    },
    readPath(notificationId) {
      return "/api/notifications/" + notificationId;
    }
  },
  methods: {
    isReply(notification) {
      return notification.data.type == "reply";
    },
    refresh({ data }) {
      this.notifications = data;
    },
    fetchData() {
      axios
        .get(this.indexPath)
        .then(response => this.refresh(response))
        .catch(error => console.log(error));
    },
    markAsRead(notificationId) {
      axios
        .delete(this.readPath(notificationId))
        .then(response => console.log(response))
        .catch(error => console.log(error));
    }
  },
  created() {
    this.fetchData();
  }
};
</script>

<style lang="scss" scoped>
</style>