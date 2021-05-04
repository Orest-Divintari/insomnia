<template>
  <div>
    <div @click="show">
      <slot></slot>
    </div>
    <modal :name="name" width="800px" height="auto">
      <div class="form-container">
        <div
          class="flex justify-between items-center bg-blue-light text-lg text-black-semi border-b border-blue-light py-3 px-5"
        >
          <p>Avatar</p>
          <button @click="hide" class="fas fa-times"></button>
        </div>
        <form enctype="multipart/form-data">
          <!-- ROW -->
          <div class="flex items-start p-4">
            <!-- LEFT -->
            <img :src="avatarPath" class="avatar-xl" />
            <!-- RIGHT -->
            <div class="ml-3 flex-1">
              <div
                @click="selectAvatar"
                class="flex flex-row-reverse items-center"
              >
                <label for="custom-avatar" class="form-label flex-1 ml-2"
                  >Use a custom avatar</label
                >
                <input
                  type="radio"
                  name="avatar-radio-button"
                  value="true"
                  ref="avatar-radio"
                  :checked="avatarIsSelected"
                />
              </div>
              <div class="ml-5.5">
                <p class="text-gray-lightest text-xs">
                  Drag this image to crop it, then click Okay to confirm, or
                  upload a new avatar below.
                </p>
                <div class="mt-5/2">
                  <label class="form-label" for="avatar-input"
                    >Upload new custom avatar:</label
                  >
                  <image-upload
                    :clearInput="avatarIsPersisted"
                    :disabled="!avatarIsSelected"
                    class="form-input"
                    :class="{ 'bg-gray-disabled': !avatarIsSelected }"
                    name="avatar"
                    @loaded="onLoad"
                  ></image-upload>
                  <p class="text-gray-lightest text-xs">
                    It is recommended that you use an image that is at least
                    400x400 pixels.
                  </p>
                </div>
              </div>
            </div>
          </div>

          <hr class="bg-blue-light my-1" />

          <div class="flex items-start p-4 text-black-semi">
            <!-- LEFT -->
            <img :src="gravatarPath" class="avatar-xl" />
            <!-- RIGHT -->
            <div class="ml-3 flex-1">
              <div
                @click="selectGravatar"
                class="flex flex-row-reverse items-center"
              >
                <label for="custom-avatar" class="form-label flex-1 ml-2"
                  >Use Gravatar</label
                >
                <input
                  type="radio"
                  ref="gravatar-radio"
                  id="gravatar-radio-button"
                  value="true"
                  :checked="gravatarIsSelected"
                />
              </div>
              <div class="ml-5.5">
                <div class="mt-5/2">
                  <input
                    :disabled="!gravatarIsSelected"
                    v-model="form.gravatar"
                    class="form-input"
                    :class="{ 'bg-gray-disabled': !gravatarIsSelected }"
                    name="gravatar"
                    v-focus="gravatarIsSelected"
                    type="email"
                    required
                  />
                  <p class="text-gray-lightest text-xs">
                    Enter the email address of the Gravatar you want to use.
                  </p>
                </div>
              </div>
            </div>
          </div>

          <div class="form-button-container justify-center">
            <button type="button" @click="persist" class="form-button">
              Okay
            </button>
            <button
              v-if="customAvatar"
              @click="destroy"
              type="button"
              class="ml-2 form-button"
            >
              Delete
            </button>
          </div>
        </form>
      </div>
    </modal>
  </div>
</template>

<script>
import authorization from "../../mixins/authorization";
import store from "../../store";
import ImageUpload from "../ImageUpload";
export default {
  components: {
    ImageUpload,
  },
  props: {
    user: {
      type: Object,
      default: {},
      required: true,
    },
    name: {
      type: String,
      default: "",
      required: true,
    },
  },
  mixins: [authorization, store],
  data() {
    return {
      avatarIsPersisted: false,
      avatarIsSelected: true,
      customAvatarPath: null,
      form: {
        avatar: "",
        gravatar: this.user.gravatar_email,
      },
    };
  },
  computed: {
    path() {
      return "/ajax/users/" + this.user.name + "/avatar";
    },
    gravatarIsSelected() {
      return !this.avatarIsSelected;
    },
    customAvatar() {
      return !store.state.visitor.default_avatar;
    },
    avatarPath() {
      return (
        this.customAvatarPath ??
        store.state.visitor.avatar_path ??
        this.user.avatar_path
      );
    },
    gravatarPath() {
      return this.user.gravatar_path;
    },
    imageIsLoaded() {
      return this.form.avatar ? true : false;
    },
  },
  methods: {
    show() {
      this.$modal.show(this.name);
    },
    hide() {
      this.$modal.hide(this.name);
    },
    selectAvatar() {
      this.avatarIsSelected = true;
    },
    selectGravatar() {
      this.avatarIsSelected = false;
    },
    persist() {
      if (this.avatarIsSelected && this.imageIsLoaded) {
        this.persistAvatar();
      } else if (this.gravatarIsSelected && this.form.gravatar) {
        this.persistGravatar();
      } else {
        this.hide();
      }
    },
    destroy() {
      axios
        .delete(this.path)
        .then(({ data }) => this.onDestroyed(data))
        .catch((error) => console.log(error.response.data));
    },
    onLoad(avatar) {
      this.avatarIsPersisted = false;
      this.customAvatarPath = avatar.src;
      this.form.avatar = avatar.file;
    },
    persistAvatar() {
      let data = this.formData(this.form.avatar);
      axios
        .post(this.path, data)
        .then(({ data }) => this.onAvatarPersisted())
        .catch((error) => showErrorModal(error.response.data));
    },
    formData(file) {
      let data = new FormData();
      data.append("avatar", file);
      data.append("_method", "patch");
      return data;
    },
    onAvatarPersisted() {
      this.onPersisted();
      this.avatarIsPersisted = true;
    },
    onDestroyed() {
      this.clearUploadedImage();
    },
    persistGravatar() {
      axios
        .patch(this.path, { gravatar_email: this.form.gravatar })
        .then(({ data }) => this.onPersisted())
        .catch((error) => console.log(error));
    },
    clearUploadedImage() {
      this.form.avatar = null;
      this.customAvatarPath = null;
    },
    onPersisted() {
      this.clearUploadedImage();
      this.hide();
    },
  },
};
</script>

<style lang="scss" scoped>
</style>