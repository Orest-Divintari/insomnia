<template>
  <div>
    <dropdown styleClasses="w-80">
      <template v-slot:dropdown-trigger>
        <div class="relative hover:bg-blue-mid h-14 text-center pt-4 px-2">
          <i class="fas fa-envelope"></i>
        </div>
      </template>
      <template v-slot:dropdown-items>
        <div class="dropdown-title">Conversations</div>

        <div class="overflow-scroll max-h-96">
          <div>
            <div v-for="(conversation, index) in conversations">
              <div
                @click="showConversation(conversation)"
                class="p-2 flex group hover:bg-white-catskill cursor-pointer border-t border-gray-lighter"
                :class="containerClasses(index)"
              >
                <img
                  :src="conversation.starter.avatar_path"
                  class="avatar-sm mr-2"
                />
                <div class="text-xs">
                  <p
                    class="text-blue-link group-hover:underline group-hover:text-blue-link-hover"
                  >
                    {{ conversation.title }}
                  </p>
                  <p class="text-xs text-gray-lightest">
                    {{ participantNames(conversation.participants) }}
                  </p>
                  <p class="text-xs text-gray-mid">
                    {{ conversation.date_updated }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="dropdown-footer-item flex items-center shadow-2xl">
          <a href="/conversations" class="blue-link">Show all</a>
          <p class="dot"></p>
          <a href="/conversations/create" class="blue-link"
            >Start a new conversation</a
          >
        </div>
      </template>
    </dropdown>
  </div>
</template>

<script>
import view from "../../mixins/view";
export default {
  data() {
    return {
      conversations: [],
      conversationsCount: 0,
    };
  },
  mixins: [view],
  computed: {
    conversationsExist() {
      return this.conversationsCount > 0;
    },
    path() {
      return "/api/conversations?recentAndUnread=true";
    },
  },
  methods: {
    containerClasses(index) {
      return [
        this.conversations[index].has_been_updated
          ? "bg-white-catskill"
          : "bg-blue-lighter",
      ];
    },
    participantNames(participants) {
      var names = "";
      for (var participant of participants) {
        names += participant.name + ", ";
      }
      return names.replace(/,(\s+)?$/, "");
    },
    refresh(data) {
      this.conversations = data;
    },
    fetchData() {
      axios
        .get(this.path)
        .then(({ data }) => this.refresh(data))
        .catch((error) => console.log(error));
    },
  },
  created() {
    this.fetchData();
  },
};
</script>

<style lang="scss" scoped>
</style>