<template>
  <div>
    <div
      class="flex justify-between bg-white-catskill rounded-t py-2 pl-1 pr-3 border-b-6 border-purple-muted"
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
import filters from "../../mixins/filters";
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
  mixins: [filters],
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
};
</script>

<style lang="scss" scoped>
</style>