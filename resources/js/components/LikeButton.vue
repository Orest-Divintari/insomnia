<template>
  <div>
    <button
      @click="toggle"
      class="btn-reply-control mr-2"
      :class="[{'text-blue-like': this.isLiked}, styleAttributes]"
    >
      <span class="fas fa-thumbs-up"></span> Like
    </button>
  </div>
</template>

<script>
export default {
  props: {
    item: {
      type: Object,
      default: {},
      required: true,
    },
    styleAttributes: {
      type: String,
      default: "",
    },
  },
  data() {
    return {
      isLiked: this.item.is_liked,
    };
  },
  computed: {
    path() {
      return "/api/replies/" + this.item.id + "/likes";
    },
  },
  methods: {
    toggle() {
      this.isLiked ? this.unlike() : this.like();
    },
    like() {
      axios
        .post(this.path)
        .then(() => {
          this.isLiked = true;
          this.$emit("liked", true);
        })
        .catch((error) => console.log(error));
    },
    unlike() {
      axios
        .delete(this.path)
        .then(() => {
          this.isLiked = false;
          this.$emit("liked", false);
        })
        .catch((error) => console.log(error));
    },
  },
};
</script>

<style lang="scss" scoped>
</style>