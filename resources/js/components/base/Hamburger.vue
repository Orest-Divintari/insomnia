<template>
  <div v-click-outside="hide">
    <div @click="display = true" class="fas fa-bars"></div>
    <div
      :class="{ 'slide-menu': display }"
      class="
        fixed
        top-0
        left-0
        hamburger-menu
        bg-white
        shadow-2xl
        text-gray-light
      "
    >
      <div class="flex flex-col justify-start">
        <div class="bg-blue-dark text-white">
          <div class="flex items-center px-4 py-3">
            <span class="flex-1 text-xl">Menu</span>
            <span @click="display = false" class="text-sm fas fa-times"></span>
          </div>
        </div>
        <a class="p-4 hover:bg-blue-lighter" href>Guides</a>
        <a class="p-4 hover:bg-blue-lighter" href>How Tos</a>
        <a class="p-4 hover:bg-blue-lighter" href>Front Page</a>
        <a class="p-4 hover:bg-blue-lighter" href>Reviews</a>
        <a class="p-4 hover:bg-blue-lighter" href>Ads</a>
        <a href="/forum" class="p-4 hover:bg-blue-lighter">Forums</a>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Hamburger",
  data() {
    return {
      display: false,
    };
  },
  methods: {
    hide() {
      this.display = false;
    },
  },
};
</script>

<style lang="scss" scoped>
.hamburger-menu {
  margin-left: -17rem;
  width: 15rem;
  // margin-left: -150px;
  height: 100vh;
  transition: transform 300ms ease-in-out;
}

.slide-menu {
  transform: translateX(17rem);
}
</style>