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
      return "<p class='bg-gray-50'> <a> mans </a> </p> <blockquote>  mans </blockquote>";

      // return '<blockquote class="blockquote"> <div class="bg-red-500"> <div class="title">ola anthira</div> <div>ola kala</div> </div></blockquote>';
    },
    quoteReply() {
      EventBus.$emit("quotedReply", this.data());
    }
  }
};
</script>

<style lang="scss" scoped>
</style>