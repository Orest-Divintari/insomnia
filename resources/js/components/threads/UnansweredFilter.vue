<template>
  <div class="dropdown-item">
    <div class="flex flex-row-reverse items-center justify-end">
      <label class="ml-3 form-label" for="unanswered">Unanswered</label>
      <input
        @click="$emit('checked', $event.target.checked , 'unanswered')"
        :checked="isChecked"
        ref="unasnwered"
        class="from-input"
        type="checkbox"
        id="unanswered"
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