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
import EventBus from "../../eventBus";
export default {
  components: {},
  props: {
    repliable: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
      body: "",
      quotedData: "",
      posted: false,
    };
  },
  computed: {
    threadReplyPath() {
      return "/api/threads/" + this.repliable.slug + "/replies";
    },
    conversationMessagePath() {
      return "/api/conversations/" + this.repliable.slug + "/messages";
    },
    path() {
      if (this.repliable.type == "conversation") {
        return this.conversationMessagePath;
      }
      return this.threadReplyPath;
    },
  },
  methods: {
    post() {
      axios
        .post(this.path, { body: this.body })
        .then(({ data }) => this.addReply(data))
        .catch((error) => showModalError(error.response.data));
    },
    addReply(data) {
      this.posted = !this.posted;
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