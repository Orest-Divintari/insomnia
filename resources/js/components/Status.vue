<template>
  <div>
    <div class="reply-container">
      <div class="reply-left-col w-24">
        <img :src="user.avatar_path" class="avatar-lg" alt />
      </div>
      <div class="reply-right-col">
        <input
          @click="isTyping=true"
          v-if="isTyping != true"
          class="p-3 rounded border border-blue-lightest focus:outline-none w-full"
          placeholder="update your status"
        />
        <wysiwyg
          v-if="isTyping"
          v-model="body"
          :style-attributes="'min-h-24'"
          placeholder="Update your status"
          :shouldClear="posted"
        ></wysiwyg>
        <button @click="post" v-if="isTyping" class="form-button px-4 mt-3">
          <span class="fas fa-reply"></span> Post
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import Wysiwyg from "../components/Wysiwyg";
export default {
  components: {
    Wysiwyg
  },
  props: {
    user: {
      type: Object,
      default: {}
    }
  },
  data() {
    return {
      body: "",
      isTyping: false,
      posted: false
    };
  },
  methods: {
    data() {
      return { body: this.body };
    },
    newPost(data) {
      this.posted = !this.posted;
      this.$emit("added", data);
    },
    post() {
      let path = "/api/profiles/" + this.user.name + "/posts";
      axios
        .post(path, this.data())
        .then(({ data }) => this.newPost(data))
        .catch(error => console.log(error));
    }
  }
};
</script>

<style lang="scss" scoped>
</style>