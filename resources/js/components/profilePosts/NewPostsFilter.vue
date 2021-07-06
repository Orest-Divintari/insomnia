<template>
  <div class="dropdown-item">
    <div class="flex flex-row-reverse items-center justify-end">
      <label class="ml-3 form-label" for="new-posts">New posts</label>
      <input
        dusk="new-posts-filter-checkbox"
        @click="$emit('checked', $event.target.checked, 'newPosts')"
        :checked="isChecked"
        ref="newPosts"
        class="from-input"
        type="checkbox"
        id="new-posts"
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