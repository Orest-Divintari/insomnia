<template>
  <div>
    <thread-filters :threadFilters="threadFilters"></thread-filters>
    <div
      v-if="pinnedThreadsExist"
      class="text-gray-700 justify-between bg-white-catskill rounded-t p-2 font-thin"
    >
      Sticky Threads
    </div>
    <thread-list
      v-if="pinnedThreadsExist"
      class="border-l-3 border-red-dark"
      @read="read"
      :threads="pinnedThreads"
    ></thread-list>
    <div
      v-if="pinnedThreadsExist"
      class="text-gray-700 justify-between bg-white-catskill rounded-t p-2 font-thin"
    >
      Normal Threads
    </div>
    <thread-list @read="read" :threads="threads"></thread-list>
    <paginator :dataset="dataset"></paginator>
  </div>
</template>

<script>
import paginator from "../Paginator";
import replies from "../../mixins/replies";
import ThreadList from "./ThreadList";
import ThreadFilters from "./ThreadFilters";
import view from "../../mixins/view";
export default {
  components: {
    paginator,
    ThreadFilters,
    ThreadList,
  },
  props: {
    paginatedThreads: Object,
    threadFilters: {
      default: {},
    },
    pinnedThreads: {
      type: Array,
      default: [],
    },
  },
  mixins: [replies, view],
  data() {
    return {
      threads: this.paginatedThreads.data,
      dataset: this.paginatedThreads,
    };
  },
  computed: {
    pinnedThreadsExist() {
      return this.pinnedThreads.length > 0;
    },
  },
  methods: {
    readPath(slug) {
      return "/api/threads/" + slug + "/read";
    },
    markAsRead(thread) {
      thread.has_been_updated = false;
    },
    read(thread) {
      this.markAsRead(thread);
      axios
        .patch(this.readPath(thread.slug))
        .catch((error) => console.log(error.response));
    },
  },

  mounted() {},
};
</script>

<style lang="scss" scoped>
</style>