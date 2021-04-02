<template>
  <div class="dropdown-item">
    <label class="form-label block pb-2" for="started-by">Started By:</label>
    <input
      @input="$emit('input', $event.target.value)"
      class="form-input bg-white"
      type="text"
      id="started-by"
      placeholder="member's name"
      dusk="started-by-filter"
    />
  </div>
</template>

<script>
export default {};
</script>

<style lang="scss" scoped>
</style>