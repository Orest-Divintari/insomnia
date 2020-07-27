<template>
  <div class="dropdown-item">
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
export default {};
</script>

<style lang="scss" scoped>
</style>