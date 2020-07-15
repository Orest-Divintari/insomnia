<template>
  <div class="dropdown-item" v-if="showFilter">
    <label class="form-label block pb-2" for="last-updated">Last Updated:</label>
    <input
      @input="$emit('input', $event.target.value)"
      class="form-input bg-white"
      type="text"
      id="last-updated"
      placeholder="number of days"
    />
  </div>
</template>

<script>
export default {
  computed: {
    showFilter() {
      return !window.location.href.includes("/threads?lastUpdated=");
    }
  }
};
</script>

<style lang="scss" scoped>
</style>