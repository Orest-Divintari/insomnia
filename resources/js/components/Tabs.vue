<template>
  <div>
    <div class>
      <ul class="flex tabs">
        <li
          class="tab"
          :class="{'is-active' : tab.isActive , 'tab-disable-hover' : tab.isActive}"
          v-for="(tab,index) in tabs"
          :key="tab.id"
          @click="selectTab(tab.name)"
        >
          <a :href="'#' + tab.hrefDescription">{{tab.name}}</a>
        </li>
      </ul>
    </div>
    <slot></slot>
  </div>
</template>

<script>
import EventBus from "../eventBus";
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
    },
  },
  mounted() {
    EventBus.$on("selectTab", (tabName) => {
      this.selectTab(tabName);
    });
  },
};
</script>

<style lang="scss" scoped>
</style>