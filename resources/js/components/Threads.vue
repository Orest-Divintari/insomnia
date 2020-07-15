<template>
  <div>
    <div class="flex bg-white-catskill rounded-t py-3 pl-1 pr-3">
      <thread-filters
        @removeFilter="removeFilter"
        v-for="(value, key) in filters"
        :key="key"
        :filter-key="key"
        :filter-value="value"
      ></thread-filters>
    </div>
    <div
      v-for="(thread, index) in data"
      :key="thread.id"
      class="border border-white-catskill"
      :class="containerClasses(index)"
    >
      <div class="flex items-center">
        <div class="py-5/2 px-5">
          <img :src="thread.poster.avatar_path" class="w-9 h-9 avatar" />
        </div>
        <div class="p-2 flex-1">
          <a
            @click="showThread(thread)"
            class="text-sm hover:underline hover:text-blue-mid cursor-pointer"
            :class="{ 'font-bold' : thread.has_been_updated }"
            v-text="thread.title"
          ></a>
          <div class="flex items-center">
            <a
              class="text-xs text-gray-lightest leading-none hover:unerline"
              v-text="thread.poster.short_name"
            ></a>
            <p class="dot"></p>
            <a
              @click="showThread(thread)"
              class="hover:underline text-xs text-gray-lightest cursor-pointer"
              v-text="thread.date_created"
            ></a>
            <p
              v-if="signedIn"
              @click="visit(thread)"
              class="text-xs text-gray-lightest ml-1 hover:underline cursor-pointer"
            >- Mark Read</p>
          </div>
        </div>
        <div class="p-2 text-gray-lightest w-32 mr-4">
          <div class="flex justify-between items-end">
            <p class="text-sm flex-1">Replies:</p>
            <p class="text-xs text-black" v-text="thread.replies_count"></p>
          </div>
          <div class="flex justify-between items-end">
            <p class="text-sm">Views:</p>
            <p class="text-xs" v-text="thread.views"></p>
          </div>
        </div>
        <div class="w-48 p-5/2 text-right">
          <p
            @click="showReply(thread.recent_reply)"
            class="text-gray-lightest text-xs hover:underline cursor-pointer"
            v-text="thread.date_updated"
          ></p>
          <p
            class="text-gray-lightest text-xs hover:underline cursor-pointer"
            v-text="thread.recent_reply.poster.short_name "
          ></p>
        </div>
        <div class="pl-1 py-5/2 pr-7">
          <a href>
            <img :src="thread.recent_reply.poster.avatar_path" class="avatar w-6 h-6" />
          </a>
        </div>
      </div>
    </div>
    <paginator :dataset="dataset"></paginator>
  </div>
</template>

<script>
import paginator from "./Paginator";
import replies from "../mixins/replies";
import ThreadFilters from "../components/ThreadFilters";
export default {
  components: {
    paginator,
    ThreadFilters
  },
  props: {
    threads: Object
  },
  mixins: [replies],
  data() {
    return {
      data: this.threads.data,
      dataset: this.threads,
      filters: {}
    };
  },
  methods: {
    containerClasses(index) {
      return [
        index % 2 == 1 ? "bg-blue-lighter" : "bg-white",
        index == 0 ? "" : "border-t-0"
      ];
    },
    endpoint(slug) {
      return "/threads/" + slug;
    },
    markAsRead(thread) {
      thread.has_been_updated = false;
    },
    showThread(thread) {
      location.href = this.endpoint(thread.slug);
    },
    visit(thread) {
      this.markAsRead(thread);
      console.log(this.endpoint(thread.slug));
      axios
        .get(this.endpoint(thread.slug))
        .catch(error => console.log(error.response));
    },
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
  },
  mounted() {}
};
</script>

<style lang="scss" scoped>
</style>