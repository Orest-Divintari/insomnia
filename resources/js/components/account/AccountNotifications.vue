<template>
  <div>
    <div class="flex justify-end pb-7/2">
      <read-all-notifications-button
        @markedAllRead="onMarkedAllRead"
        button-classes="px-2
          py-1
          bg-white
          border border-gray-lighter
          rounded
          text-blue-mid text-smaller
          focus:outline-none
          active:text-blue-mid-light"
      >
      </read-all-notifications-button>
    </div>
    <div v-if="notificationsExist" v-cloak>
      <div class="border border-gray-lighter rounded" v-cloak>
        <div v-for="(notification, index) in items" :key="notification.id">
          <notification
            class="p-7/2 border-t-0 text-sm text-black-semi"
            :notification="notification"
            hover-background=""
            :class="notificationClasses(index)"
            :read-all="readAll"
            read-background="bg-white"
          ></notification>
        </div>
      </div>
      <paginator class="mt-2" :dataset="dataset"></paginator>
    </div>
    <p v-else class="border border-gray-lighter rounded p-7/2 text-sm">
      You do not have any recent notifications.
    </p>
  </div>
</template>

<script>
import Notification from "../notifications/Notification.vue";
import ReadAllNotificationsButton from "../notifications/ReadAllNotificationsButton.vue";
export default {
  name: "AccountNotifications",
  components: { Notification, ReadAllNotificationsButton },
  props: {
    dataset: {
      type: Object,
      required: true,
    },
    user: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      items: this.dataset.data,
      readAll: false,
    };
  },
  computed: {
    notificationsExist() {
      return this.items.length > 0;
    },
  },
  methods: {
    notificationClasses(index) {
      return [this.items.length - 1 == index ? "border-t" : ""];
    },
    onMarkedAllRead() {
      this.readAll = true;
    },
  },
};
</script>

<style lang="scss" scoped>
</style>