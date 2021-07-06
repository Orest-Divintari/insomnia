<template>
  <div>
    <div class="flex justify-between">
      <div
        v-for="(value, key) in filters"
        :key="key"
        @click="removeFilter(key)"
      >
        <div class="flex">
          <div class="mr-1/2">
            <div
              class="
                flex
                items-center
                ml-2
                bg-gray-geyser
                rounded
                py-1/2
                px-3
                hover:bg-gray-loblolly
                hover:text-blue-ship-cove
                text-gray-700 text-xs
              "
            >
              <button>
                {{ getFilterKey(key, value) }}:
                <span class="text-black-semi">{{
                  getFilterValue(key, value)
                }}</span>
              </button>
              <i class="ml-1 pt-1/2 fas fa-times"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from "moment";
export default {
  props: {
    filters: {
      default: {},
    },
  },
  methods: {
    getFilterKey(key, value) {
      if (typeof value === "boolean") {
        return "Show only";
      }
      return this.format(key);
    },
    format(item) {
      let words = item.split(/(?=[A-Z])/);
      let lowerCaseWords = this.lowerCase(words);
      return this.capitalize(lowerCaseWords);
    },
    lowerCase(words) {
      return words.map((word) => word.toLowerCase());
    },
    capitalize(words) {
      words[0] = words[0][0].toUpperCase() + words[0].substring(1);
      return words.join(" ");
    },
    getFilterValue(key, value) {
      if (typeof value === "boolean") {
        return this.format(key);
      } else if (this.isNumeric(value)) {
        return moment().subtract(parseInt(value), "day").fromNow();
      }
      return value;
    },
    removeFilter(filterKey) {
      this.$emit("removeFilter", filterKey);
    },
    isNumeric(value) {
      return !isNaN(value);
    },
  },
};
</script>

<style lang="scss" scoped>
</style>