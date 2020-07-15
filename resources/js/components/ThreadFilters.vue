<template>
  <div>
    <div class="flex justify-between bg-white-catskill rounded-t py-2 pl-1 pr-3">
      <div class="flex">
        <thread-filter-tags
          @removeFilter="removeFilter"
          v-for="(value, key) in filters"
          :key="key"
          :filter-key="key"
          :filter-value="value"
        ></thread-filter-tags>
      </div>
      <dropdown :styleClasses="'w-80'">
        <template v-slot:dropdown-trigger>
          <div
            class="cursor-pointer p-3/2 text-blue-mid text-xs hover:text-blue-ship-cove hover:bg-gray-loblolly rounded"
          >
            Filters
            <span class="pb-1 fas fa-sort-down"></span>
          </div>
        </template>
        <template v-slot:dropdown-items>
          <div class="dropdown-title">Show only</div>
          <unanswered-filter @checked="unanswered = $event.target.checked"></unanswered-filter>
          <started-by-filter v-model="startedBy"></started-by-filter>
          <updated-by-filter v-model="updatedBy"></updated-by-filter>
          <last-updated-filter v-model="lastUpdated"></last-updated-filter>
        </template>
      </dropdown>
    </div>
  </div>
</template>

<script>
import ThreadFilterTags from "../components/ThreadFilterTags";
import StartedByFilter from "../components/StartedByFilter";
import UpdatedByFilter from "../components/UpdatedByFilter";
import LastUpdatedFilter from "../components/LastUpdatedFilter";
import UnansweredFilter from "../components/UnansweredFilter";
export default {
  components: {
    ThreadFilterTags,
    StartedByFilter,
    UpdatedByFilter,
    LastUpdatedFilter,
    UnansweredFilter
  },
  data() {
    return {
      filters: {},
      startedBy: "",
      updatedBy: "",
      lastUpdated: "",
      unanswered: ""
    };
  },
  methods: {
    findFilters() {
      var matches = window.location.href.matchAll(/\?([\w]+)(?:=)([\w]+)/gi);
      for (let match of matches) {
        var key = match[1];
        var value = match[2];
        var filter = { key: value };
        this.filters[key] = value;
      }
    },
    removeFilter(key) {
      Vue.delete(this.filters, key);
      this.updateFilters();
    },
    updateFilters() {
      var path = window.location.pathname;
      for (const [key, value] of Object.entries(this.filters)) {
        path = path + "?" + key + "=" + value + "&";
      }
      window.location.href = path;
    }
  },
  created() {
    this.findFilters();
  }
};
</script>

<style lang="scss" scoped>
</style>