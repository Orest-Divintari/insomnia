<template>
  <div class="dropdown-item">
    <label class="form-label block pb-2" for="started-by">Received By:</label>
    <names-autocomplete-input
      dusk="received-by-filter"
      input-placeholder="member's name"
      v-model="names"
      @input="broadcastInput"
    ></names-autocomplete-input>
  </div>
</template>

<script>
export default {
  data() {
    return {
      names: "",
    };
  },
  methods: {
    broadcastInput() {
      this.$emit("input", this.names);
    },
  },
};
</script>

<style lang="scss" scoped>
</style>