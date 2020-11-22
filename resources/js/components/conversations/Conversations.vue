<template>
  <div>
    <div
      v-for="(conversation, conversationsIndex) in conversations"
      :key="conversation.id"
      class="border border-white-catskill"
      :class="containerClasses(conversationsIndex)"
    >
      <div class="flex items-center">
        <div class="py-5/2 px-5">
          <img
            @click="showProfile(conversation.starter)"
            :src="conversation.starter.avatar_path"
            class="cursor-pointer w-9 h-9 avatar"
          />
        </div>
        <div class="p-2 flex-1">
          <a
            @click="showConversation(conversation)"
            class="text-sm hover:underline hover:text-blue-mid cursor-pointer"
            :class="{ 'font-bold': conversation.has_been_updated }"
            v-text="conversation.title"
          ></a>
          <div class="flex items-center">
            <div
              class="flex items-center"
              v-for="(participant,
              participantsIndex) in conversation.participants"
            >
              <a
                @click="showProfile(participant)"
                class="mr-1 text-xs text-gray-lightest leading-none hover:underline cursor-pointer"
                v-text="
                  participantNames(
                    participant,
                    participantsIndex,
                    conversation.participants.length
                  )
                "
              ></a>
            </div>

            <p class="dot"></p>
            <a
              @click="showConversation(conversation)"
              class="hover:underline text-xs text-gray-lightest cursor-pointer"
              v-text="conversation.date_created"
            ></a>
          </div>
        </div>
        <div class="p-2 text-gray-lightest w-40 mr-4">
          <div class="flex flex-col justify-between items-end">
            <div class="flex items-center">
              <p class="text-sm">Replies:</p>
              <p
                class="ml-6 text-black-semi text-smaller"
                v-text="conversation.messages_count"
              ></p>
            </div>
            <div class="flex items-center">
              <p class="text-sm">Participants:</p>
              <p
                class="ml-6 text-black-semi text-smaller"
                v-text="conversation.participants_count"
              ></p>
            </div>
          </div>
        </div>
        <div class="w-56 p-5/2 text-right">
          <p
            @click="showMessage(conversation.recent_message)"
            class="text-gray-lightest text-xs hover:underline cursor-pointer"
            v-text="conversation.recent_message.date_updated"
          ></p>
          <a
            @click="showProfile(conversation.recent_message.poster)"
            class="text-gray-lightest text-xs hover:underline cursor-pointer"
            v-text="conversation.recent_message.poster.short_name"
          ></a>
        </div>
      </div>
    </div>
    <paginator :dataset="dataset"></paginator>
  </div>
</template>

<script>
import paginator from "../Paginator";
import views from "../../mixins/view";
export default {
  components: {
    paginator,
  },
  props: {
    paginatedConversations: {
      type: Object,
      required: false,
    },
    conversationFilters: {
      type: Object,
      required: false,
    },
  },
  mixins: [views],
  data() {
    return {
      conversations: this.paginatedConversations.data,
      dataset: this.paginatedConversations,
    };
  },
  methods: {
    participantNames(participant, index, total) {
      if (index < total - 1) {
        return participant.name + ",";
      }
      return participant.name;
    },
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