<template>
  <div class="mt-6">
    <div class="reply-container">
      <div class="reply-left-col">
        <img :src="user.avatar_path" class="avatar-xl" alt />
      </div>
      <div class="reply-right-col">
        <form @submit.prevent="post">
          <wysiwyg
            v-model="body"
            :style-attributes="'reply-form'"
            placeholder="Write your reply..."
            :quoted-data="quotedData"
            :shouldClear="posted"
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
  components: {},
  data() {
    return {
      body: "",
      quotedData: "",
      posted: false,
    };
  },
  computed: {
    path() {
      return "/api" + window.location.pathname + "/replies";
    },
  },
  methods: {
    post() {
      axios
        .post(this.path, { body: this.body })
        .then(({ data }) => this.addReply(data))
        .catch((error) => console.log(error.response));
    },
    addReply(data) {
      this.posted = true;
      this.$emit("created", data);
      this.body = "";
    },
  },
  mounted() {
    EventBus.$on("quotedReply", (quotedData) => {
      this.quotedData = quotedData;
    });
  },
};
</script>

<style lang="scss" scoped>
</style>