<template>
  <div>
    <div
      v-for="(thread, index) in threads"
      :key="thread.id"
      class="border border-blue-border"
      :class="classes(index)"
    >
      <div class="flex items-center">
        <div class="py-5/2 px-5">
          <img :src="thread.poster.avatar_path" class="w-9 h-9 avatar" />
        </div>
        <div class="p-2 flex-1">
          <a
            @click="showThread(thread.slug)"
            class="text-sm hover:underline hover:text-blue-mid cursor-pointer"
            :class="{ 'font-bold': !thread.has_been_updated }"
            v-text="thread.title"
          ></a>
          <div class="flex items-center">
            <a
              class="text-xs text-gray-lightest leading-none hover:unerline"
              v-text="thread.poster.short_name"
            ></a>
            <p class="dot"></p>
            <a
              @click="showThread(thread.slug)"
              class="hover:underline text-xs text-gray-lightest cursor-pointer"
              v-text="thread.date_created"
            ></a>
            <p
              @click="markAsRead(thread)"
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
            <p class="text-xs">2</p>
          </div>
        </div>
        <div class="w-48 p-5/2 text-right">
          <p
            @click="showReply(thread)"
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
            <img :src="thread.recent_reply.poster.avatar_path" class="avatar w-6 h-6" />
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["threads"],
  methods: {
    classes(index) {
      return [
        index % 2 == 1 ? "bg-blue-light" : "bg-white",
        index == 0 ? "" : "border-t-0"
      ];
    },
    showThread(slug) {
      location.href = "/threads/" + slug;
    },
    showReply(thread) {
      location.href = "/threads/" + thread.slug + "#" + thread.recent_reply.id;
    },
    markAsRead(thread) {}
  }
};
</script>

<style lang="scss" scoped>
</style>