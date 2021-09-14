<template>
  <div>
    <button
      dusk="follow-list-button"
      @click="show"
      class="text-smaller blue-link hover:underline cursor"
    >
      ... and {{ membersCount }} more.
    </button>
    <modal :name="name" height="auto">
      <p class="sticky top-0 p-4 bg-white-catskill text-lg text-black-semi">
        {{ title }}
      </p>

      <div class="flex flex-col overflow-y-scroll h-112">
        <div
          v-for="(member, index) in members"
          class="flex items-start"
          :class="classes(index)"
        >
          <img
            @click="showProfile(member)"
            :src="member.avatar_path"
            class="cursor-pointer avatar-lg"
          />
          <div class="pl-4">
            <a
              @click="showProfile(member)"
              class="blue-link font-bold text-md"
              v-text="member.name"
            ></a>
            <p class="text-smaller text-black-semi">macrumors member</p>
            <div class="flex items-center text-xs text-gray-lightest">
              <p>Messages: {{ member.profile_posts_count }}</p>
              <p class="dot"></p>
              <p>Like score: {{ member.received_likes_count }}</p>
              <p class="dot"></p>
              <p>Points: 0</p>
            </div>
          </div>
        </div>
      </div>
      <fetch-more-button
        class="sticky bottom-0"
        v-if="hasMore"
        @fetchMore="fetchMore"
        title="More..."
      ></fetch-more-button>
    </modal>
  </div>
</template>

<script>
import FetchMoreButton from "../base/FetchMoreButton";
import fetch from "../../mixins/fetch";
import view from "../../mixins/view";
export default {
  components: {
    FetchMoreButton,
  },
  props: {
    followList: {
      type: Object,
      default: {},
    },
    title: {
      type: String,
      default: "",
    },
    name: {
      type: String,
      default: "",
    },
  },
  mixins: [fetch, view],
  data() {
    return {
      members: [...this.followList.data],
      dataset: this.followList,
    };
  },
  computed: {
    membersCount() {
      return this.dataset.total - this.dataset.per_page;
    },
  },
  methods: {
    classes(index) {
      return [
        "p-4",
        "border",
        index == this.members.length - 1 ? "border-b" : "border-b-0",
      ];
    },
    show() {
      this.$modal.show(this.name);
    },
    hide() {
      this.$modal.hide(this.name);
    },
    refresh(paginatedCollection) {
      this.dataset = paginatedCollection;
      this.members.push(...paginatedCollection.data);
    },
  },
};
</script>

<style lang="scss" scoped>
</style>