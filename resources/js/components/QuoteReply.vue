<template>
  <div @click="quoteReply">
    <button class="btn-reply-control">
      <span class="fas fa-reply"></span>
      Reply
    </button>
  </div>
</template>

<script>
import EventBus from "../eventBus";
export default {
  props: {
    reply: {
      type: Object,
      default: {}
    }
  },
  methods: {
    data() {
      return '<blockquote class="blockquote"> <div class="container"> <div class="title">ola anthira</div> <div>ola kala</div> </div></blockquote>';
    },
    quoteReply() {
      EventBus.$emit("quotedReply", this.data());
    }
  }
};
</script>

<style lang="scss" scoped>
</style>