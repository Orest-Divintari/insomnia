<template>
  <div @click="quoteReply">
    <button class="btn-reply-control">
      <span class="fas fa-reply"></span>
      Reply
    </button>
  </div>
</template>

<script>
import EventBus from "../../eventBus";
export default {
  props: {
    reply: {
      type: Object,
      default: {},
    },
    replyNumber: {
      type: Number,
    },
    perPage: {
      type: Number,
    },
    currentPage: {
      type: Number,
    },
  },
  methods: {
    data() {
      return (
        "<blockquote> <a href=" +
        this.goToReply +
        ">" +
        this.reply.poster.name +
        " said to post " +
        this.reply.position +
        " </a> " +
        "</blockquote> <br><br>"
      );
    },
    quoteReply() {
      EventBus.$emit("quotedReply", this.data());
    },
  },
  computed: {
    goToReply() {
      if (
        this.replyNumber > this.perPage * this.currentPage - this.perPage &&
        this.replyNumber < this.perPage * this.currentPage
      ) {
        return (
          window.location.pathname +
          "?page=" +
          this.currentPage +
          "#post-" +
          this.reply.id
        );
      }
      return "/ajax/replies/" + this.reply.id;
    },
  },
};
</script>

<style lang="scss" >
</style>