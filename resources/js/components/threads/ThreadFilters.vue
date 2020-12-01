<template>
  <div>
    <div
      class="flex justify-between bg-white-catskill rounded-t py-2 pl-1 pr-3"
    >
      <div class="flex">
        <filter-labels
          @removeFilter="removeFilter"
          :filters="filters"
        ></filter-labels>
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
          <unanswered-filter
            v-if="showUnanswered"
            :is-checked="form.unanswered"
            @checked="toggle"
          ></unanswered-filter>
          <watched-filter
            :is-checked="form.watched"
            @checked="toggle"
          ></watched-filter>
          <posted-by-filter v-model="form.postedBy"></posted-by-filter>
          <updated-by-filter v-model="form.updatedBy"></updated-by-filter>
          <last-updated-filter v-model="form.lastUpdated"></last-updated-filter>
          <last-created-filter v-model="form.lastCreated"></last-created-filter>
          <div class="text-right dropdown-item">
            <button @click="apply" class="form-button-small">Filter</button>
          </div>
        </template>
      </dropdown>
    </div>
  </div>
</template>

<script>
import ThreadFilterTags from "./ThreadFilterTags";
import FilterLabels from "../filters/FilterLabels";
import PostedByFilter from "./PostedByFilter";
import UpdatedByFilter from "./UpdatedByFilter";
import LastUpdatedFilter from "./LastUpdatedFilter";
import LastCreatedFilter from "./LastCreatedFilter";
import UnansweredFilter from "./UnansweredFilter";
import WatchedFilter from "./WatchedFilter";
export default {
  components: {
    FilterLabels,
    PostedByFilter,
    UpdatedByFilter,
    LastUpdatedFilter,
    LastCreatedFilter,
    UnansweredFilter,
    WatchedFilter,
  },
  props: {
    threadFilters: {
      default: {},
    },
  },
  data() {
    return {
      filters: this.threadFilters,
      form: {
        postedBy: "",
        updatedBy: "",
        lastUpdated: "",
        lastCreated: "",
        unanswered: false,
        watched: false,
      },
    };
  },
  computed: {
    showUnanswered() {
      return !this.form.trending == true;
    },
  },
  methods: {
    toggle(isChecked, filterType) {
      if (this.filters[filterType] == true) {
        this.removeFilter(filterType);
      } else {
        this.filters[filterType] = isChecked;
      }
    },
    removeFilter(filterType) {
      Vue.delete(this.filters, filterType);
      delete this.form[filterType];
    },
    apply() {
      this.updateFilters();
      var path = window.location.pathname + "?";
      for (const [key, value] of Object.entries(this.filters)) {
        path = path + key + "=" + value + "&";
      }
      window.location.href = path;
    },
    updateFilters() {
      for (var filter in this.form) {
        if (this.form[filter] != "") {
          this.filters[filter] = this.form[filter];
        }
      }
    },
  },
  created() {
    for (var filter in this.filters) {
      this.form[filter] = this.filters[filter];
    }
  },
};
</script>

<style lang="scss" scoped>
</style>