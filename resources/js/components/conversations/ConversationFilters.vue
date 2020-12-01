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
          <unread-filter
            :is-checked="form.unread"
            @checked="toggle"
          ></unread-filter>
          <started-by-filter v-model="form.startedBy"></started-by-filter>
          <received-by-filter v-model="form.receivedBy"></received-by-filter>
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
import StartedByFilter from "./StartedByFilter";
import ReceivedByFilter from "./ReceivedByFilter";
import UnreadFilter from "./UnreadFilter";

export default {
  components: {
    FilterLabels,
    StartedByFilter,
    ReceivedByFilter,
    UnreadFilter,
  },
  props: {
    conversationFilters: {
      default: {},
    },
  },
  mixins: [filters],
  data() {
    return {
      filters: this.conversationFilters,
      form: {
        startedBy: "",
        receivedBy: "",
        unread: false,
      },
    };
  },
};
</script>

<style lang="scss" scoped>
</style>