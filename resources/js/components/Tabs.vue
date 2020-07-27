<template>
  <div>
    <div class>
      <ul class="flex tabs">
        <li
          class="tab"
          :class="{'is-active' : tab.isActive , 'tab-disable-hover' : tab.isActive}"
          v-for="(tab,index) in tabs"
          :key="tab.id"
          @click="selectTab(tab)"
        >
          <a :href="'#' + tab.hrefDescription">{{tab.name}}</a>
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
    selectTab(selectedTab) {
      this.tabs.forEach((tab) => {
        tab.isActive = tab.name == selectedTab.name;
      });
    },
  },
};
</script>

<style lang="scss" scoped>
</style>