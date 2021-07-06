<template>
  <div dusk="by-following-filter" class="dropdown-item">
    <div class="flex flex-row-reverse items-center justify-end">
      <label class="ml-3 form-label" for="by-following-members"
        >Members you follow</label
      >
      <input
        dusk="by-following-filter-checkbox"
        @click="$emit('checked', $event.target.checked, 'byFollowing')"
        :checked="isChecked"
        ref="byFollowingMembers"
        class="from-input"
        type="checkbox"
        id="by-following-members"
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