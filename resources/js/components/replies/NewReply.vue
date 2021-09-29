<template>
  <div
    class="mt-6"
    dusk="new-reply-input"
    v-if="can('add_reply', repliableData)"
  >
    <div class="reply-container">
      <div class="reply-left-col">
        <profile-popover
          :user="authUser"
          trigger="avatar"
          triggerClasses="avatar-xl"
        ></profile-popover>
      </div>
      <div class="w-full p-3">
        <form @submit.prevent="post" class="relative">
          <wysiwyg
            v-model="body"
            :style-attributes="'reply-form'"
            placeholder="Write your reply..."
            :quoted-data="quotedData"
            :shouldClear="posted"
          ></wysiwyg>
          <button
            dusk="post-reply-button"
            type="submit"
            class="mt-4 form-button"
          >
            Post Reply
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import EventBus from "../../eventBus";
import authorizable from "../../mixins/authorizable";
export default {
  props: {
    repliable: {
      type: Object,
      default: {},
    },
  },
  mixins: [authorizable],
  data() {
    return {
      repliableData: this.repliable,
      body: "",
      quotedData: "",
      posted: false,
    };
  },
  computed: {
    threadReplyPath() {
      return "/ajax/threads/" + this.repliable.slug + "/replies";
    },
    conversationMessagePath() {
      return "/ajax/conversations/" + this.repliable.slug + "/messages";
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
        .catch((error) => showErrorModal(error.response.data));
    },
    addReply(data) {
      this.posted = !this.posted;
      this.$emit("created", data);
      this.body = "";
    },
  },
  watch: {
    repliable(newValue, oldValue) {
      this.repliableData = newValue;
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