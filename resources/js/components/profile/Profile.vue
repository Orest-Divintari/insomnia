<template>
  <div dusk="profile-component">
    <div class="relative flex border border-gray-lighter rounded">
      <div class="bg-blue-light absolute h-20 p-4 w-full"></div>
      <avatar class="p-4" :user="user"></avatar>

      <div class="w-full flex-1 p-4 pl-0 z-0">
        <div class="leading-none">
          <p class="text-2xl pb-3" v-text="user.name"></p>
          <p class="text-sm">Macrumors newbie</p>
        </div>
        <div class="flex justify-around pt-8">
          <div>
            <p class="text-xs text-gray-lightest">Messages</p>
            <p
              dusk="messages-count"
              class="text-md text-center"
              v-text="user.profile_posts_count"
            ></p>
          </div>
          <div>
            <p class="text-xs text-gray-lightest">Likes Score</p>
            <p
              dusk="likes-count"
              class="text-md text-center"
              v-text="user.received_likes_count"
            ></p>
          </div>
          <div>
            <p class="text-xs text-gray-lightest">Points</p>
            <p dusk="points" class="text-md text-center">0</p>
          </div>
        </div>

        <div>
          <hr class="mt-7/2 mb-6" />
          <div class="pr-4 text-sm">
            <div class="flex justify-between items-center">
              <div class="flex">
                <p>
                  <span class="text-gray-lightest">Joined:</span>
                  {{ user.join_date }}
                </p>
                <p class="self-center dot"></p>
                <p>
                  Viewing member profile
                  <a
                    class="italic hover:underline text-blue-like"
                    :href="'/profiles/' + user.name"
                    v-text="user.name"
                  ></a>
                </p>
              </div>
              <div class="flex">
                <follow-button
                  class="mr-1"
                  v-if="!authorize('is', user) && signedIn"
                  :profile-owner="user"
                  :followed="user.followed_by_visitor"
                ></follow-button>
                <ignore-user-button
                  class="mr-1"
                  v-if="!authorize('is', user) && signedIn"
                  :profile-owner="user"
                  :ignored="user.ignored_by_visitor"
                ></ignore-user-button>

                <start-conversation-button
                  class="mr-1"
                  :user="profileOwner"
                ></start-conversation-button>
                <dropdown :styleClasses="'w-56'">
                  <template v-slot:dropdown-trigger>
                    <div class="btn-white-blue flex items-center">
                      <p>Find</p>
                      <span class="ml-1 fas fa-caret-down"></span>
                    </div>
                  </template>
                  <template v-slot:dropdown-items>
                    <div class="dropdown-title">Find content</div>
                    <div class="dropdown-item">
                      <a :href="'/search?posted_by=' + user.name"
                        >Find all content by {{ user.name }}
                      </a>
                    </div>
                    <a
                      :href="'/search?type=thread&posted_by=' + user.name"
                      class="dropdown-item"
                      >Find all threads by {{ user.name }}</a
                    >
                  </template>
                </dropdown>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <tabs class="mt-5">
      <tab name="Profile Posts" :selected="true">
        <profile-posts-tab
          :paginated-posts="posts"
          :profile-owner="user"
        ></profile-posts-tab>
      </tab>
      <tab name="Latest Activity">
        <latest-activity-tab :profile-owner="user"></latest-activity-tab>
      </tab>
      <tab name="Postings">
        <posting-activity-tab :profile-owner="user"></posting-activity-tab>
      </tab>
      <tab name="About">
        <about-tab :profile-owner="user"></about-tab>
      </tab>
    </tabs>
  </div>
</template>

<script>
import ProfilePostsTab from "./ProfilePostsTab";
import LatestActivityTab from "./LatestActivityTab";
import PostingActivityTab from "./PostingActivityTab";
import StartConversationButton from "../conversations/StartConversationButton";
import AboutTab from "./AboutTab";
import Tabs from "../base/Tabs";
import Tab from "../base/Tab";
import authorizable from "../../mixins/authorizable";

export default {
  name: "Profile",
  components: {
    ProfilePostsTab,
    StartConversationButton,
    Tabs,
    Tab,
    LatestActivityTab,
    PostingActivityTab,
    AboutTab,
  },
  props: {
    profileOwner: {
      type: Object,
      required: true,
    },
    posts: {
      type: Object,
      default: {},
    },
  },
  mixins: [authorizable],
  computed: {
    canUpdate() {
      return this.hover && this.ownsProfile(this.user);
    },
  },
  data() {
    return {
      user: this.profileOwner,
      hover: false,
    };
  },
  methods: {},
};
</script>

<style lang="scss" scoped>
</style>