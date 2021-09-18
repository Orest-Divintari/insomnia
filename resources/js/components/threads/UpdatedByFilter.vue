<template>
  <div class="dropdown-item" v-if="showFilter">
    <label class="form-label block pb-2" for="started-by">Updated By:</label>
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
    showFilter() {
      return !window.location.href.includes("/threads?contributed=");
    },
  },
  methods: {
    broadcastInput() {
      this.$emit("input", this.user);
    },
  },
};
</script>

<style lang="scss" scoped>
</style>