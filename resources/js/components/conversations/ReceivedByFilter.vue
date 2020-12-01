<template>
  <div class="dropdown-item">
    <label class="form-label block pb-2" for="started-by">Received By:</label>
    <input
      @input="$emit('input', $event.target.value)"
      class="form-input bg-white"
      type="text"
      id="received-by"
      placeholder="member's name"
    />
  </div>
</template>

<script>
export default {};
</script>

<style lang="scss" scoped>
</style>