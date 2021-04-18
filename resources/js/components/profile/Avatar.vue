<template>
  <div>
    <div class="w-56" @mouseenter="hover" @mouseleave="stopHovering">
      <span class="absolute">
        <img
          :src="user.avatar_path"
          class="avatar-2xl text-center relative"
          alt="avatar"
        />

        <edit-user-avatar-modal
          @updated-avatar="onUpdatedAvatar"
          :user="user"
          :name="name"
        >
          <a
            v-if="canUpdate"
            class="cursor-pointer bottom-0 pt-24 text-center w-48 h-3/4 absolute edit-background-gradient text-semi-white text-sm"
          >
            Edit
          </a>
        </edit-user-avatar-modal>
      </span>
    </div>
  </div>
</template>

<script>
import authorization from "../../mixins/authorization";
import store from "../../store";
import EditUserAvatarModal from "./EditUserAvatarModal";
export default {
  components: {
    EditUserAvatarModal,
  },
  props: {
    user: {
      type: Object,
      default: {},
      required: true,
    },
  },
  mixins: [authorization],
  computed: {
    name() {
      return "edit-avatar-" + this.$parent.$options.name + "-modal";
    },
    canUpdate() {
      return this.isHovering && this.ownsProfile(this.user);
    },
  },
  data() {
    return {
      state: store.state,
      isHovering: false,
    };
  },
  methods: {
    hover() {
      this.isHovering = true;
    },
    stopHovering() {
      this.isHovering = false;
    },
    onUpdatedAvatar(user) {
      this.$emit("updated-avatar", user);
    },
  },
};
</script>

<style lang="scss" scoped>
.edit-background-gradient {
  background-image: linear-gradient(
    to bottom,
    rgba(0, 0, 0, 0) 60%,
    rgba(0, 0, 0, 0.9) 100%
  );
}
</style>
