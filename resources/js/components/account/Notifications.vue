<template>
  <div>
    <div v-if="notificationsExist" v-cloak>
      <div class="border border-t-0 border-gray-lighter rounded" v-cloak>
        <div v-for="(notification, index) in items" :key="notification.id">
          <notification
            class="p-7/2 border-t text-sm text-black-semi"
            :notification="notification"
            hover-background=""
            :class="notificationClasses(index)"
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
import Paginator from "../Paginator.vue";
import Notification from "../notifications/Notification.vue";
export default {
  components: { Paginator, Notification },
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
  },
};
</script>

<style lang="scss" scoped>
</style>