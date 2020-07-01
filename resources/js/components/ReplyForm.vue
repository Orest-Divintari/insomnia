<template>
  <div class="mt-6">
    <div class="reply-container">
      <div class="reply-left-col">
        <img :src="user.avatar_path" class="avatar-xl" alt />
      </div>
      <div class="w-full p-3">
        <form @submit.prevent="post">
          <wysiwyg
            v-model="body"
            name="body"
            classes="h-32 text-sm"
            placeholder="Write your reply..."
          ></wysiwyg>

          <button type="submit" class="mt-4 form-button">Post Reply</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import EventBus from "../eventBus";
export default {
  data() {
    return {
      body: ""
    };
  },
  computed: {
    path() {
      return "/api" + window.location.pathname + "/replies";
    }
  },
  methods: {
    post() {
      axios
        .post(this.path, { body: this.body })
        .then(({ data }) => this.addReply(data))
        .catch(error => console.log(error.response));
    },
    addReply(data) {
      EventBus.$emit("newReply", data);
      this.body = "";
    }
  }
};
</script>

<style lang="scss" scoped>
</style>