<template>
  <div>
    <button
      @click="toggle"
      class="btn-reply-control mr-2"
      :class="{'text-blue-like': this.currentReply.is_liked }"
    >
      <span class="fas fa-thumbs-up"></span> Like
    </button>
  </div>
</template>

<script>
export default {
  props: {
    reply: {
      type: Object,
      default: {}
    }
  },
  data() {
    return {
      currentReply: this.reply
    };
  },
  computed: {
    path() {
      return "/api/replies/" + this.reply.id + "/likes";
    }
  },
  methods: {
    toggle() {
      this.currentReply.is_liked ? this.unlike() : this.like();
    },
    like() {
      axios
        .post(this.path)
        .then(() => {
          this.currentReply.is_liked = true;
          this.$emit("like", true);
        })
        .catch(error => console.log(error));
    },
    unlike() {
      axios
        .delete(this.path)
        .then(() => {
          this.currentReply.is_liked = false;
          this.$emit("like", false);
        })
        .catch(error => console.log(error));
    }
  }
};
</script>

<style lang="scss" scoped>
</style>