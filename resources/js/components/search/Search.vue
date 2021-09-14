<template>
  <div v-click-outside="hide" class="pr-3">
    <div @click="display" v-show="!isOpen" class="fas fa-search"></div>
    <input
      bg-white
      v-show="isOpen"
      class="ml-2 w-full text-gray-900 py-2 px-3 rounded focus:outline-none"
      v-model="input"
      type="text"
      placeholder="Search"
    />
  </div>
</template>

<script>
export default {
  name: "Search",
  data() {
    return {
      input: "",
      isOpen: false,
    };
  },
  methods: {
    display() {
      this.isOpen = true;
    },
    hide() {
      this.isOpen = false;
    },
  },
};
</script>

<style lang="scss" scoped>
</style>