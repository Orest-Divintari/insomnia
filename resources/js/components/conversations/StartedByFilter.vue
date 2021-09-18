<template>
  <div class="dropdown-item">
    <label class="form-label block pb-2" for="started-by">Started By:</label>
    <names-autocomplete-input
      dusk="started-by-filter"
      input-placeholder="member's name"
      input-name="started_by"
      input-id="started-by-conversation-filter"
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