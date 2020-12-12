<template>
  <div class="dropdown-item">
    <div class="flex flex-row-reverse items-center justify-end">
      <label class="ml-3 form-label" for="starred">Starred</label>
      <input
        @click="$emit('checked', $event.target.checked, 'starred')"
        :checked="isChecked"
        ref="starred"
        class="from-input"
        type="checkbox"
        id="starred"
      />
    </div>
  </div>
</template>

<script>
export default {
  props: {
    isChecked: {
      default: false,
    },
  },
};
</script>

<style lang="scss" scoped>
</style>