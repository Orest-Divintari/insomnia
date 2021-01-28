<template>
  <div>
    <div class="border border-gray-lighter rounded relative">
      <div class="bg-white-catskill p-4 flex">
        <div class="w-56">
          <img
            :src="profileOwner.avatar_path"
            class="avatar-2xl absolute"
            alt="avatar"
          />
        </div>
        <div>
          <p class="text-2xl" v-text="profileOwner.name"></p>
          <p class="text-sm">Macrumors newbie</p>
        </div>
      </div>
      <div class="flex">
        <div class="w-56"></div>
        <div class="w-full flex-1">
          <div class="flex justify-between p-4">
            <div>
              <p class="text-xs text-gray-lightest">Messages</p>
              <p
                class="text-md text-center"
                v-text="profileOwner.message_count"
              ></p>
            </div>
            <div>
              <p class="text-xs text-gray-lightest">Likes Score</p>
              <p
                class="text-md text-center"
                v-text="profileOwner.likes_count"
              ></p>
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
                    {{ profileOwner.join_date }}
                  </p>
                  <p class="self-center dot"></p>
                  <p>
                    Viewing member profile
                    <a
                      class="italic hover:underline text-blue-like"
                      :href="'/profiles/' + profileOwner.name"
                      v-text="profileOwner.name"
                    ></a>
                  </p>
                </div>
                <div class="flex">
                  <follow-button
                    class="mr-1"
                    v-if="!authorize('is', profileOwner) && signedIn"
                    :profileOwner="profileOwner"
                    :is-followed-by-auth-user="profileOwner.followed_by_visitor"
                  ></follow-button>
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
                        <a :href="'/search?postedBy=' + profileOwner.name"
                          >Find all content by {{ profileOwner.name }}
                        </a>
                      </div>
                      <a
                        :href="
                          '/search?type=thread&postedBy=' + profileOwner.name
                        "
                        class="dropdown-item"
                        >Find all threads by {{ profileOwner.name }}</a
                      >
                    </template>
                  </dropdown>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <tabs class="mt-5">
      <tab name="Profile Posts" :selected="true">
        <profile-posts :profile-owner="profileOwner"></profile-posts>
      </tab>
      <tab name="Latest Activity">
        <latest-activity :profile-owner="profileOwner"></latest-activity>
      </tab>
      <tab name="Postings">
        <posting-activity :profile-owner="profileOwner"></posting-activity>
      </tab>
      <tab name="About">
        <about :profile-owner="profileOwner"></about>
      </tab>
    </tabs>
  </div>
</template>

<script>
import ProfilePosts from "./ProfilePosts";
import LatestActivity from "./LatestActivity";
import PostingActivity from "./PostingActivity";
import FollowButton from "./FollowButton";
import About from "./About";
import Tabs from "../Tabs";
import Tab from "../Tab";

export default {
  components: {
    ProfilePosts,
    Tabs,
    Tab,
    LatestActivity,
    PostingActivity,
    About,
    FollowButton,
  },
  props: {
    profileOwner: {
      type: Object,
      default: {},
    },
  },
};
</script>

<style lang="scss" scoped>
</style>