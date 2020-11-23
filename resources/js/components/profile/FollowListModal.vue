<template>
  <div>
    <button
      @click="
        show();
        fetchMore();
      "
      class="text-smaller blue-link hover:underline cursor"
    >
      ... and {{ membersCount }} more.
    </button>
    <modal
      :name="name"
      :scrollable="true"
      height="500px"
      classes="overflow-y-auto"
    >
      <p class="sticky top-0 p-4 bg-white-catskill text-lg text-black-semi">
        {{ title }}
      </p>

      <div class="flex flex-col overflow-y-auto">
        <div
          v-for="(member, index) in items"
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
              <p>Messages: {{ member.message_count }}</p>
              <p class="dot"></p>
              <p>Like score: {{ member.like_score }}</p>
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
import FetchMoreButton from "../profile/FetchMoreButton";
import fetch from "../../mixins/fetch";
import view from "../../mixins/view";
export default {
  components: {
    FetchMoreButton,
  },
  props: {
    data: {
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
      items: [],
      dataset: this.data,
    };
  },
  computed: {
    hasMore() {
      return this.dataset.next_page_url ? true : false;
    },
    membersCount() {
      return this.dataset.total - this.dataset.per_page;
    },
  },
  methods: {
    classes(index) {
      return [
        "p-4",
        "border",
        index == this.items.length - 1 ? "border-b" : "border-b-0",
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
      this.items.unshift(...paginatedCollection.data);
    },
  },
};
</script>

<style lang="scss" scoped>
</style>