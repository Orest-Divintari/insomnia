<template>
  <div>
    <conversation-filters
      :conversationFilters="conversationFilters"
    ></conversation-filters>
    <div
      v-for="(conversation, conversationsIndex) in conversations"
      :key="conversation.id"
      class="border border-white-catskill"
      :class="containerClasses(conversationsIndex)"
    >
      <div class="flex items-center">
        <div class="py-5/2 px-5">
          <profile-popover
            :user="conversation.starter"
            trigger="avatar"
            triggerClasses="avatar-md"
          >
          </profile-popover>
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
              v-for="(
                participant, participantsIndex
              ) in conversation.participants"
            >
              <profile-popover
                triggerClasses="mr-1 leading-none text-xs text-gray-lightest"
                :user="participant"
                :triggerText="
                  participantNames(
                    participant,
                    participantsIndex,
                    conversation.participants.length
                  )
                "
              ></profile-popover>
            </div>

            <p class="dot"></p>
            <a
              @click="showConversation(conversation)"
              class="hover:underline text-xs text-gray-lightest cursor-pointer"
              v-text="conversation.date_created"
            ></a>
          </div>
        </div>
        <div v-if="conversation.starred" class="pt-3/2 self-start pr-2">
          <i class="fas fa-star text-2xs text-blue-mid"></i>
        </div>
        <div class="p-2 text-gray-lightest w-40 mr-4">
          <div class="flex flex-col justify-between items-start">
            <div class="flex items-center w-full">
              <p class="text-sm flex-1">Replies:</p>
              <p
                class="ml-6 text-black-semi text-smaller"
                v-text="conversation.messages_count"
              ></p>
            </div>
            <div class="flex items-center w-full">
              <p class="flex-1 text-sm">Participants:</p>
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
          <profile-popover
            :user="conversation.recent_message.poster"
            triggerClasses="leading-relaxed text-gray-lightest text-xs"
          ></profile-popover>
        </div>
      </div>
    </div>
    <paginator :dataset="dataset"></paginator>
  </div>
</template>

<script>
import views from "../../mixins/view";
import ConversationFilters from "./ConversationFilters";
export default {
  components: {
    ConversationFilters,
  },
  props: {
    paginatedConversations: {
      type: Object,
      required: false,
    },
    conversationFilters: {
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