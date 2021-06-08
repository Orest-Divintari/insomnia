<template>
  <div>
    <profile-popover
      :user="follower"
      popover-classes="inline"
      triggerClasses="text-blue-link text-smaller"
      class="inline"
    >
    </profile-popover>
    <p class="inline">is now following you.</p>
  </div>
</template>

<script>
import view from "../../mixins/view";
export default {
  props: {
    notificationData: {
      type: Object,
      default: {},
    },
  },
  mixins: [view],
  data() {
    return {
      ...this.notificationData,
    };
  },
};
</script>

<style lang="scss" scoped>
</style>