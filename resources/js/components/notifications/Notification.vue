<template>
  <div>
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
        <div class="dropdown-title">Alerts</div>
        <div v-if="notificationsExist">
          <div
            @click="markAsRead(notification.id)"
            v-for="(notification, index) in notifications"
            :key="notification.id"
            class="dropdown-notification-item"
          >
            <component
              :is="notification.data.type"
              :notification-data="notification.data"
            ></component>
          </div>
        </div>
        <div v-else>
          <div class="dropdown-notification-item">You have no new alerts</div>
        </div>
      </template>
    </dropdown>
  </div>
</template>

<script>
import ThreadReplyNotification from "./ThreadReplyNotification";
import ReplyLikeNotification from "./ReplyLikeNotification";
import ProfilePostNotification from "./ProfilePostNotification";
import PostCommentNotification from "./PostCommentNotification";
import CommentLikeNotification from "./CommentLikeNotification";
import MessageLikeNotification from "./MessageLikeNotification";
import FollowNotification from "./FollowNotification";
import replies from "../../mixins/replies";
import fetch from "../../mixins/fetch";
import store from "../../store";
export default {
  components: {
    ThreadReplyNotification,
    ReplyLikeNotification,
    ProfilePostNotification,
    PostCommentNotification,
    CommentLikeNotification,
    FollowNotification,
    MessageLikeNotification,
  },
  mixins: [replies, fetch],
  data() {
    return {
      state: store.state,
      notifications: [],
    };
  },
  computed: {
    path() {
      return "/ajax/notifications";
    },
    unviewedCount() {
      return this.state.visitor.unviewed_notifications;
    },
    unviewedNotifications() {
      return this.unviewedCount > 0;
    },
    notificationsExist() {
      return this.notifications.length > 0;
    },
  },
  methods: {
    readPath(notificationId) {
      return "/ajax/notifications/" + notificationId;
    },
    refresh(data) {
      this.notifications = data;
    },
    markAsRead(notificationId) {
      axios
        .delete(this.readPath(notificationId))
        .then((response) => notification.length)
        .catch((error) => console.log(error));
    },
  },
  created() {
    this.fetchData();
  },
};
</script>

<style lang="scss" scoped>
</style>