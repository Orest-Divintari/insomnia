<template>
  <div class="dropdown-item" v-if="showFilter">
    <label class="form-label block pb-2" for="started-by">Started By:</label>
    <names-autocomplete-input
      v-model="user"
      @input="broadcastInput"
      input-placeholder="member's name"
      :suggestions-number="1"
    ></names-autocomplete-input>
  </div>
</template>

<script>
export default {
  data() {
    return {
      user: "",
    };
  },
  computed: {
    broadcastInput(event) {
      this.$emit("input", this.user);
    },
    showFilter() {
      return !window.location.href.includes("/threads?posted_by=");
    },
  },
};
</script>

<style lang="scss" scoped>
</style>