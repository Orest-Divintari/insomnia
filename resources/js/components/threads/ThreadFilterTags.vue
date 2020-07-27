<template>
  <div @click="removeFilter">
    <div class="flex justify-between">
      <div class="flex">
        <div class="mr-1/2">
          <div
            class="flex items-center ml-2 bg-gray-geyser rounded py-1/2 px-3 hover:bg-gray-loblolly hover:text-blue-ship-cove text-gray-700 text-xs"
          >
            <button>
              {{ title }}:
              <span class="text-black-semi">{{ value }}</span>
            </button>
            <i class="ml-1 pt-1/2 fas fa-times"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    filterKey: {
      default: ""
    },
    filterValue: {
      default: ""
    }
  },
  data() {
    return {
      description: {
        unread: { "Show Only": "unread" },
        constributed: { Contributed: this.filterValue },
        unanswered: { "Show only ": "unanswered" },
        newPosts: { "Show only": "new posts" },
        newThreads: { "Show only": "new threads" },
        startedBy: { "Started by": this.filterValue },
        watched: { "Show only": "Watched" },
        trending: { "Show only": "Trending" },
        lastUpdated: { "Last Updated": this.filterValue + " days" },
        lastCreated: { "Last Created": this.filterValue + " days" }
      }
    };
  },
  computed: {
    title() {
      return Object.keys(this.description[this.filterKey])[0];
    },
    value() {
      return Object.values(this.description[this.filterKey])[0];
    }
  },
  methods: {
    removeFilter() {
      this.$emit("removeFilter", this.filterKey);
    }
  }
};
</script>

<style lang="scss" scoped>
</style>