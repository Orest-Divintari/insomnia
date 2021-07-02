<template>
  <div dusk="ignore-user-button">
    <button @click="toggleIgnore" class="btn-white-blue flex items-center">
      <p v-if="isIgnored">Unignore</p>
      <p v-else>Ignore</p>
    </button>
  </div>
</template>

<script>
import authorizable from "../../mixins/authorizable";
export default {
  props: {
    profileOwner: {
      type: Object,
      default: {},
      required: true,
    },
    ignored: {
      required: true,
    },
  },
  mixins: [authorizable],
  data() {
    return {
      isIgnored: this.ignored,
    };
  },
  computed: {
    path() {
      return "/ajax/users/" + this.profileOwner.name + "/ignoration";
    },
  },
  watch: {
    ignored(newValue, oldValue) {
      this.isIgnored = newValue;
    },
  },
  methods: {
    toggleIgnore() {
      if (this.isIgnored) {
        this.unignore();
      } else {
        this.ignore();
      }
    },
    ignore() {
      axios
        .post(this.path)
        .then(() => this.refresh())
        .catch((error) => console.log(error));
    },
    unignore() {
      axios
        .delete(this.path)
        .then(() => this.refresh())
        .catch((error) => console.log(error));
    },
    refresh() {
      this.isIgnored = !this.isIgnored;
      this.$emit("ignore", this.isIgnored);
    },
  },
};
</script>

<style lang="scss" scoped>
</style>