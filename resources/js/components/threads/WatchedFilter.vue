<template>
  <div class="dropdown-item">
    <div class="flex flex-row-reverse items-center justify-end">
      <label class="ml-3 form-label" for="watched">Watched</label>
      <input
        @click="$emit('checked', $event.target.checked, 'watched')"
        :checked="isChecked"
        ref="unasnwered"
        class="from-input"
        type="checkbox"
        id="watched"
      />
    </div>
  </div>
</template>

<script>
export default {
  props: {
    isChecked: {
      default: false
    }
  }
};
</script>

<style lang="scss" scoped>
</style>