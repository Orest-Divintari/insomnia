<template>
  <div class="leading-snug">
    <div
      v-for="(thread, index) in threads"
      :key="thread.id"
      class="border border-white-catskill"
      :class="containerClasses(index)"
    >
      <thread-list-item :thread="thread"></thread-list-item>
    </div>
  </div>
</template>

<script>
import view from "../../mixins/view";
export default {
  components: {},
  props: {
    threads: {
      type: Array,
      default: {},
    },
    styleClasses: {
      type: String,
      default: "",
    },
  },
  mixins: [view],
  methods: {
    containerClasses(index) {
      return [
        index % 2 == 1 ? "bg-blue-lighter" : "bg-white",
        index == 0 ? "" : "border-t-0",
      ];
    },
  },
};
</script>

<style lang="scss" scoped>
.tooltip-arrow {
  width: 0;
  height: 0;
  border-style: solid;
  position: absolute;
  margin: 5px;
  border-color: black;
  z-index: 1;
}
</style>