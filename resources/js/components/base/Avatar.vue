<template>
  <div>
    <div class="relative">
      <img
        @mouseenter="showEditButton"
        @mouseleave="hideEditButton"
        :src="avatarPath"
        :class="avatarClasses"
        alt="avatar"
      />
      <edit-user-avatar-modal
        v-if="signedIn && ownsProfile(user)"
        :user="user"
        :name="name"
      >
        <button
          type="button"
          @mouseenter="showEditButton"
          @mouseleave="hideEditButton"
          v-if="canUpdate"
          class="
            z-10
            absolute
            bottom-0
            text-center
            edit-background-gradient
            text-semi-white
            cursor-pointer
            focus:outline-none
          "
          :class="buttonClasses"
        >
          Edit
        </button>
      </edit-user-avatar-modal>
    </div>
  </div>
</template>

<script>
import authorizable from "../../mixins/authorizable";
import store from "../../store";
import EditUserAvatarModal from "../account/EditUserAvatarModal";
export default {
  name: "Avatar",
  components: {
    EditUserAvatarModal,
  },
  props: {
    avatarClasses: {
      type: String,
      default: "w-48 h-48",
    },
    buttonClasses: {
      type: String,
      default: "w-48 h-24 text-sm",
    },
    user: {
      type: Object,
      default: {},
      required: true,
    },
  },
  mixins: [authorizable, store],
  computed: {
    name() {
      return "edit-avatar-" + this.$parent.$options.name + "-modal";
    },
    canUpdate() {
      return this.isHovering && this.ownsProfile(this.user);
    },
    avatarPath() {
      if (this.signedIn && this.isAuthUser(this.user)) {
        return this.state.visitor.avatar_path;
      }
      return this.user.avatar_path;
    },
  },
  data() {
    return {
      state: store.state,
      isHovering: false,
    };
  },
  methods: {
    showEditButton() {
      this.isHovering = true;
    },
    hideEditButton() {
      this.isHovering = false;
    },
  },
};
</script>

<style lang="scss" scoped>
.edit-background-gradient {
  background-image: linear-gradient(
    to bottom,
    rgba(0, 0, 0, 0) 10%,
    rgba(0, 0, 0, 0.9) 100%
  );
}
</style>
