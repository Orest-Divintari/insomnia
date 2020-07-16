<template>
  <div class="dropdown-item" v-if="showFilter">
    <label class="form-label block pb-2" for="started-by">Updated By:</label>
    <input
      @input="$emit('input', $event.target.value)"
      class="form-input bg-white"
      type="text"
      id="updated-by"
      placeholder="member's name"
    />
  </div>
</template>

<script>
export default {
  computed: {
    showFilter() {
      return !window.location.href.includes("/threads?contributed=");
    }
  }
};
</script>

<style lang="scss" scoped>
</style>