<template>
  <div>
    <dropdown styleClasses="w-80">
      <template v-slot:dropdown-trigger>
        <div class="relative hover:bg-blue-mid h-14 text-center pt-4 px-2">
          <i class="fas fa-bell"></i>
          <i
            v-if="notificationsExist"
            class="bg-red-700 rounded-full absolute left-1/2 -mt-1 text-2xs font-black shadow-lg text-white"
            v-text="notificationCount"
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
            <reply-like-notification
              v-if="isReplyLike(notification)"
              :notification-data="notification.data"
            ></reply-like-notification>

            <thread-reply-notification
              v-if="isReply(notification)"
              :notification-data="notification.data"
            ></thread-reply-notification>

            <post-comment-notification
              v-if="isPostComment(notification)"
              :notification-data="notification.data"
            ></post-comment-notification>

            <profile-post-notification
              v-if="isProfilePost(notification)"
              :notification-data="notification.data"
            ></profile-post-notification>

            <comment-like-notification
              v-if="isCommentLike(notification)"
              :notification-data="notification.data"
            ></comment-like-notification>
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

import replies from "../../mixins/replies";
export default {
  components: {
    ThreadReplyNotification,
    ReplyLikeNotification,
    ProfilePostNotification,
    PostCommentNotification,
    CommentLikeNotification,
  },
  mixins: [replies],
  data() {
    return {
      notifications: [],
      notificationCount: 0,
    };
  },
  computed: {
    indexPath() {
      return "/api/notifications";
    },
    notificationsExist() {
      return this.notificationCount > 0;
    },
  },
  methods: {
    readPath(notificationId) {
      return "/api/notifications/" + notificationId;
    },
    isReply(notification) {
      return notification.data.type == "reply";
    },
    isReplyLike(notification) {
      return notification.data.type == "replyLike";
    },
    isCommentLike(notification) {
      return notification.data.type == "commentLike";
    },
    isProfilePost(notification) {
      return notification.data.type == "profilePost";
    },
    isPostComment(notification) {
      return notification.data.type == "postComment";
    },
    refresh({ data }) {
      this.notifications = data;
      this.notificationCount = this.notifications.length;
    },
    fetchData() {
      axios
        .get(this.indexPath)
        .then((response) => this.refresh(response))
        .catch((error) => console.log(error));
    },
    markAsRead(notificationId) {
      this.notificationCount--;
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