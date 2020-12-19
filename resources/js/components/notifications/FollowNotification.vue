<template>
  <div class="flex items-center" @click="showProfile(follower)">
    <img :src="follower.avatar_path" class="avatar-sm mr-2" alt="" />
    <div>
      <p>
        <a class="blue-link"> {{ follower.name }} </a> is now following you.
      </p>
      <p class="gray-lightest">{{ notificationData.date_created }}</p>
    </div>
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
      following: this.notificationData.following,
      follower: this.notificationData.follower,
    };
  },
};
</script>

<style lang="scss" scoped>
</style>