<template>
  <div>
    <div class="border border-gray-lighter rounded relative">
      <div class="bg-white-catskill p-4 flex">
        <div class="w-56">
          <img :src="profileUser.avatar_path" class="avatar-2xl absolute" alt="avatar" />
        </div>
        <div>
          <p class="text-2xl" v-text="profileUser.name"></p>
          <p class="text-sm">Macrumors newbie</p>
        </div>
      </div>
      <div class="flex">
        <div class="w-56"></div>
        <div class="w-full flex-1">
          <div class="flex justify-between p-4">
            <div>
              <p class="text-xs text-gray-lightest">Messages</p>
              <p class="text-md text-center" v-text="profileUser.messages_count"></p>
            </div>
            <div>
              <p class="text-xs text-gray-lightest">Likes Score</p>
              <p class="text-md text-center" v-text="profileUser.likes_score"></p>
            </div>
            <div>
              <p class="text-xs text-gray-lightest">Points</p>
              <p class="text-md text-center">0</p>
            </div>
          </div>

          <div>
            <hr />
            <div class="p-4 text-sm">
              <div class="flex justify-between">
                <div class="flex">
                  <p>
                    <span class="text-gray-lightest">Joined:</span>
                    {{ profileUser.join_date }}
                  </p>
                  <p class="self-center dot"></p>
                  <p>
                    Viewing member profile
                    <a
                      class="italic hover:underline text-blue-like"
                      :href="'/profiles/' + profileUser.name"
                      v-text="profileUser.name"
                    ></a>
                  </p>
                </div>
                <dropdown>
                  <template v-slot:dropdown-trigger>
                    <div class="btn-white-blue flex items-center">
                      <p>Find</p>
                      <span class="ml-1 fas fa-caret-down"></span>
                    </div>
                  </template>
                  <template v-slot:dropdown-items>
                    <div class="dropdown-title">Find content</div>
                    <div class="dropdown-item">Find all content by {{ user.name }}</div>
                    <a
                      :href="'/threads?startedBy=' + profileUser.name"
                      class="dropdown-item"
                    >Find all threads by {{ profileUser.name }}</a>
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
        <profile-posts :profile-user="profileUser"></profile-posts>
      </tab>
      <tab name="Latest Activity"></tab>
      <tab name="Postings">
        <recent-content></recent-content>
      </tab>
      <tab name="About">
        <about></about>
      </tab>
    </tabs>
  </div>
</template>

<script>
import ProfilePosts from "./ProfilePosts";
import LatestActivity from "./LatestActivity";
import RecentContent from "./RecentContent";
import About from "./About";
import Tabs from "../Tabs";
import Tab from "../Tab";

export default {
  components: {
    ProfilePosts,
    Tabs,
    Tab,
    LatestActivity,
    RecentContent,
    About,
  },
  props: {
    profileUser: {
      type: Object,
      default: {},
    },
  },
};
</script>

<style lang="scss" scoped>
</style>