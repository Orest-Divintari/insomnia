<template>
  <div class="dropdown-item" v-if="showFilter">
    <label class="form-label block pb-2" for="started-by">Started By:</label>
    <input
      @input="$emit('input', $event.target.value)"
      class="form-input bg-white"
      type="text"
      id="started-by"
      placeholder="member's name"
    />
  </div>
</template>

<script>
export default {
  computed: {
    showFilter() {
      return !window.location.href.includes("/threads?by=");
    }
  }
};
</script>

<style lang="scss" scoped>
</style>