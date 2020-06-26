<template>
  <div>
    <div :id="'post-'+ reply.id" class="reply-container">
      <div class="reply-left-col">
        <img :src="reply.poster.avatar_path" class="avatar-large" alt />
        <a
          href
          class="mt-1 text-blue-mid text-sm hover:underline font-bold"
          v-text="reply.poster.name"
        ></a>
        <p
          v-if="reply.poster.name == threadPoster"
          class="bg-green-mid rounded text-white border border-green-900 px-7 text-xs font-hairline"
        >Original Poster</p>
        <i class="mt-2 fas fa-chevron-down"></i>
      </div>
      <div class="w-full">
        <div class="flex justify-between items-center">
          <a
            :href="'#post-'+reply.id"
            v-text="reply.date_created"
            class="text-xs text-gray-lightest hover:underline font-hairline pl-3"
          ></a>
          <div class="flex items-center text-xs text-gray-lightest">
            <a class="mr-3 fas fa-share-alt"></a>
            <a class="mx-3 far fa-bookmark"></a>
            <div class="bg-blue-reply-border text-white px-5/2 py-2">#{{ index + 1 }}</div>
          </div>
        </div>
        <div class="p-5/2 h-full">
          <div class="flex flex-col h-full pb-8">
            <div class="flex-1 text-black-semi text-sm pr-48">
              <highlight :content="reply.body"></highlight>
            </div>
            <div class="flex justify-between">
              <button class="btn-reply-control">
                <i class="fas fa-exclamation-circle"></i>
                Report
              </button>
              <div class="flex">
                <button class="btn-reply-control mr-2">
                  <span class="fas fa-thumbs-up"></span> Like
                </button>
                <button class="btn-reply-control">
                  <span class="fas fa-reply"></span>
                  Reply
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Highlight from "./Highlight";
export default {
  components: {
    Highlight
  },
  props: {
    threadPoster: {
      type: String,
      default: ""
    },
    index: {
      type: Number,
      default: 1
    },
    reply: {
      type: Object,
      default: {}
    }
  }
};
</script>

<style lang="scss" scoped>
</style>