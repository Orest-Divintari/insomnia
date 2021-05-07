<template>
  <div>
    <div v-if="notificationsExist">
      <div class="border border-gray-lighter rounded">
        <div
          v-for="(notification, index) in items"
          :key="notification.id"
          class="bg-white-catskill p-7/2 text-sm"
          :class="notificationClasses(index)"
        >
          <component
            :is="notification.data.type"
            :notification-data="notification.data"
          ></component>
        </div>
      </div>
      <paginator class="mt-2" :dataset="dataset"></paginator>
    </div>
    <p v-else class="border border-gray-lighter rounded p-7/2 text-sm">
      You do not have any recent alerts.
    </p>
  </div>
</template>

<script>
import Paginator from "../Paginator.vue";
export default {
  components: { Paginator },
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
      return [
        this.items.length - 1 == index ? "" : "border-b border-gray-lighter",
      ];
    },
  },
};
</script>

<style lang="scss" scoped>
</style>