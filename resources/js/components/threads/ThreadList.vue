<template>
  <div>
    <div
      v-for="(thread, index) in threads"
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
            :class="{ 'font-bold': thread.has_been_updated }"
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
              v-if="signedIn && thread.has_been_updated"
              @click="$emit('read', thread)"
              class="text-xs text-gray-lightest ml-1 hover:text-black hover:underline cursor-pointer"
            >
              - Mark Read
            </p>
            <div class="ml-1 flex items-center leading-0">
              <a
                v-for="(link, page) in thread.last_pages"
                :key="index"
                :href="link"
                class="btn-paginator cursor-pointer px-1 mx-1/2 text-xs"
              >
                {{ page }}
              </a>
            </div>
          </div>
        </div>
        <i
          v-if="thread.pinned"
          class="fas fa-thumbtack text-2xs text-gray-lightest self-baseline mb-5 mr-2"
        ></i>
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
            v-text="thread.recent_reply.poster.short_name"
          ></p>
        </div>
        <div class="pl-1 py-5/2 pr-7">
          <a href>
            <img
              :src="thread.recent_reply.poster.avatar_path"
              class="avatar w-6 h-6"
            />
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import view from "../../mixins/view";
export default {
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
</style>