<template>
  <div></div>
</template>

<script>
import Comment from "./Comment";
import NewComment from "./NewComment";
export default {
  props: {
    post: {
      type: Object,
      default: {},
      required: true
    },
    components: {
      Comment,
      NewComment
    }
  },
  data() {
    return {
      comments: []
    };
  }
};
</script>

<style lang="scss" scoped>
</style>