<template>
  <div>
    <div>
      <ul class="flex tabs">
        <li
          v-for="(tab, index) in tabs"
          :key="tab.id"
          @click="selectTab(tab.name)"
        >
          <a
            class="block tab cursor-pointer"
            :class="{
              'is-active': tab.isActive,
              'tab-disable-hover': tab.isActive,
            }"
            :href="'#' + tab.hrefDescription"
            >{{ tab.name }}</a
          >
        </li>
      </ul>
    </div>
    <slot></slot>
  </div>
</template>

<script>
export default {
  data() {
    return {
      tabs: [],
    };
  },
  created() {
    this.tabs = this.$children;
  },
  methods: {
    selectTab(selectedTabName) {
      this.tabs.forEach((tab) => {
        tab.isActive = tab.name == selectedTabName;
      });
      window.location.href;
    },
    previouslySelectedTab() {
      this.tabs.forEach((tab) => {
        if (window.location.href.includes(tab.hrefDescription)) {
          this.selectTab(tab.name);
        }
      });
    },
  },
  mounted() {
    this.previouslySelectedTab();
  },
};
</script>

<style lang="scss" scoped>
</style>