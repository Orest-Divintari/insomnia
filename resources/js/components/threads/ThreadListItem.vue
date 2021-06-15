<template>
  <div>
    <div class="flex items-center">
      <div class="pb-3 pt-2 pl-5">
        <profile-popover
          :user="thread.poster"
          trigger="avatar"
          triggerClasses="avatar-md"
        ></profile-popover>
      </div>
      <div class="p-5/2 flex-1">
        <p
          @click="showThread(thread)"
          class="text-sm hover:underline hover:text-blue-mid cursor-pointer"
          :class="{ 'font-bold': thread.has_been_updated }"
          v-text="thread.title"
        ></p>
        <div class="flex items-center">
          <profile-popover
            :user="thread.poster"
            triggerClasses="text-xs text-gray-lightest"
          >
          </profile-popover>
          <p class="dot"></p>
          <a
            @click="showThread(thread)"
            class="hover:underline text-xs text-gray-lightest cursor-pointer"
            v-text="thread.date_created"
          ></a>
          <p
            v-if="signedIn && thread.has_been_updated"
            @click="$emit('read', thread)"
            class="
              text-xs text-gray-lightest
              ml-1
              hover:text-black
              hover:underline
              cursor-pointer
            "
          >
            - Mark Read
          </p>
          <div class="ml-1 flex items-center leading-0">
            <a
              v-for="(link, page) in thread.last_pages"
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
        class="
          fas
          fa-thumbtack
          text-2xs text-gray-lightest
          self-baseline
          mb-5
          mr-2
        "
      ></i>
      <div class="p-5/2 text-gray-lightest w-32 mr-4">
        <div class="flex justify-between items-end">
          <p class="text-sm flex-1">Replies:</p>
          <p class="text-xs text-black" v-text="thread.replies_count"></p>
        </div>
        <div class="flex justify-between items-end">
          <p class="text-smaller">Views:</p>
          <p class="text-xs" v-text="thread.views"></p>
        </div>
      </div>
      <div class="w-48 p-5/2 text-right">
        <p
          @click="showReply(thread.recent_reply)"
          class="text-gray-lightest text-xs hover:underline cursor-pointer"
          v-text="thread.date_updated"
        ></p>
        <profile-popover
          triggerClasses="leading-relaxed text-gray-lightest text-xs"
          :user="thread.recent_reply.poster"
        ></profile-popover>
      </div>
      <div class="pl-1 py-5/2 pr-7">
        <profile-popover
          :user="thread.recent_reply.poster"
          trigger="avatar"
          triggerClasses="avatar-sm"
        ></profile-popover>
      </div>
    </div>
  </div>
</template>
<script>
import view from "../../mixins/view";
export default {
  props: {
    thread: {
      type: Object,
      default: {},
    },
  },
  mixins: [view],
};
</script>

<style lang="scss" scoped>
</style>