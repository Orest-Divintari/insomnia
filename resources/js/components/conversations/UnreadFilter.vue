<template>
  <div class="dropdown-item">
    <div class="flex flex-row-reverse items-center justify-end">
      <label class="ml-3 form-label" for="unread">Unread</label>
      <input
        @click="$emit('checked', $event.target.checked, 'unread')"
        :checked="isChecked"
        ref="unread"
        class="from-input"
        name="unread"
        type="checkbox"
        id="unread"
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