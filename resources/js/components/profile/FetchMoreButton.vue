<template>
  <div>
    <div class="flex justify-end bg-white-catskill py-3">
      <button
        class="w-32 focus:outline-none text-blue-mid py-2 text-xs mr-3 font-semibold hover:bg-blue-botticelli rounded"
        @click="$emit('fetchMore')"
      >
        {{ title }}
      </button>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    title: {
      type: String,
      default: "",
    },
  },
};
</script>

<style lang="scss" scoped>
</style>