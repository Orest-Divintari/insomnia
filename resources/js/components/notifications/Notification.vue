<template>
  <div
    class="flex notification-item break-words"
    :class="[
      read ? readBackground : unreadBackground,
      hovering ? hoverBackground : '',
    ]"
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
          {{ notificationDate }}
        </p>
        <read-notification-button
          @toggleRead="toggleRead"
          :hovering="hovering"
          :is-read="read"
          :notification="notification"
        >
        </read-notification-button>
      </div>
    </div>
  </div>
</template>

<script>
import moment from "moment";
import ReadNotificationButton from "./ReadNotificationButton";
import ThreadReplyNotification from "./ThreadReplyNotification";
import ReplyLikeNotification from "./ReplyLikeNotification";
import PostCommentNotification from "./PostCommentNotification";
import ProfilePostNotification from "./ProfilePostNotification";
import CommentLikeNotification from "./CommentLikeNotification";
import ProfilePostLikeNotification from "./ProfilePostLikeNotification";
import MessageLikeNotification from "./MessageLikeNotification";
import FollowNotification from "./FollowNotification";
import ProfilePostMentionNotification from "./mention/ProfilePostMentionNotification";
import CommentMentionNotification from "./mention/CommentMentionNotification";
import ThreadMentionNotification from "./mention/ThreadMentionNotification";
import ThreadReplyMentionNotification from "./mention/ThreadReplyMentionNotification";

export default {
  components: {
    ProfilePostMentionNotification,
    CommentMentionNotification,
    ThreadMentionNotification,
    ThreadReplyMentionNotification,
    ReplyLikeNotification,
    ThreadReplyNotification,
    ReadNotificationButton,
    PostCommentNotification,
    ProfilePostNotification,
    CommentLikeNotification,
    ProfilePostLikeNotification,
    MessageLikeNotification,
    FollowNotification,
  },
  props: {
    readAll: {
      type: Boolean,
      default: false,
    },
    notification: {
      type: Object,
      required: true,
    },
    readBackground: {
      type: String,
      default: "bg-blue-lighter",
    },
    unreadBackground: {
      type: String,
      default: "bg-white-catskill",
    },
    hoverBackground: {
      type: String,
      default: "bg-white-catskill",
    },
  },
  data() {
    return {
      hovering: false,
      read: this.notification.read_at !== null,
    };
  },
  computed: {
    path() {
      return "/ajax/notifications/" + this.notification.id + "/read";
    },
    notificationDate() {
      return moment(this.notification.created_at).calendar();
    },
  },
  watch: {
    readAll(newValue, oldValue) {
      if (newValue) {
        this.read = true;
      }
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