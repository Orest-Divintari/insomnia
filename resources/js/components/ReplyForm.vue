<template>
  <div class="mt-6">
    <div class="reply-container">
      <div class="reply-left-col">
        <img :src="user.avatar_path" class="avatar-large" alt />
      </div>
      <div class="w-full p-3">
        <form @submit.prevent="post">
          <wysiwyg
            :clearInput="posted"
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
export default {
  data() {
    return {
      body: "",
      posted: false
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
      console.log(data);
      this.$emit("newReply", data);
      this.body = "";
      this.posted = true;
    }
  }
};
</script>

<style lang="scss" scoped>
</style>