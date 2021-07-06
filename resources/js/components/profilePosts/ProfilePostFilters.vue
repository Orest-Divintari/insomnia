<template>
  <div>
    <div
      class="
        flex
        justify-between
        bg-white-catskill
        rounded-t
        py-2
        pl-1
        pr-3
        border-b-6 border-purple-muted
      "
    >
      <div class="flex">
        <filter-labels
          @removeFilter="removeFilter"
          :filters="filters"
        ></filter-labels>
      </div>
      <dropdown :styleClasses="'w-80'">
        <template v-slot:dropdown-trigger>
          <button
            dusk="profile-post-filters-dropdown-button"
            class="
              cursor-pointer
              p-3/2
              text-blue-mid text-xs
              hover:text-blue-ship-cove
              hover:bg-gray-loblolly
              rounded
            "
          >
            Filters
            <span class="pb-1 fas fa-sort-down"></span>
          </button>
        </template>
        <template v-slot:dropdown-items>
          <div class="dropdown-title">Show only</div>
          <by-following-members-filter
            @checked="toggle"
            :is-checked="form.byFollowing"
          ></by-following-members-filter>
          <new-posts-filter
            :is-checked="form.newPosts"
            @checked="toggle"
          ></new-posts-filter>
          <div class="text-right dropdown-footer-item">
            <button
              dusk="apply-filters-button"
              @click="apply"
              class="form-button-small"
            >
              Filter
            </button>
          </div>
        </template>
      </dropdown>
    </div>
  </div>
</template>

<script>
import filters from "../../mixins/filters";
import FilterLabels from "../filters/FilterLabels";
import NewPostsFilter from "../profilePosts/NewPostsFilter.vue";
import ByFollowingMembersFilter from "../profilePosts/ByFollowingMembersFilter.vue";
export default {
  components: {
    FilterLabels,
    NewPostsFilter,
    ByFollowingMembersFilter,
  },
  props: {
    profilePostFilters: {
      default: {},
    },
  },
  mixins: [filters],
  data() {
    return {
      filters: this.profilePostFilters,
      form: {
        byFollowing: false,
        newPosts: false,
      },
    };
  },
};
</script>

<style lang="scss" scoped>
</style>