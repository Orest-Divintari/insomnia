<template>
  <div dusk="profile-popover">
    <v-popover
      :class="popoverClasses"
      popoverClass="focus:outline-none"
      class="leading-none text-xs"
      placement="top"
      :open="isOpen"
      trigger="manual"
      delay="0"
    >
      <div
        dusk="profile-popover-trigger"
        @click.stop="showProfile(profileOwner)"
        class="cursor-pointer"
        @mouseover="show"
        @mouseout="hide"
      >
        <img
          v-if="trigger == 'avatar'"
          :src="avatarPath"
          :class="triggerClasses"
        />
        <a
          v-else
          :class="triggerClasses"
          class="hover:underline"
          v-text="username"
        >
        </a>
      </div>
      <!-- This will be the content of the popover -->
      <template slot="popover">
        <div
          class="border border-gray-lighter -m-6 shadow-2xl"
          @mouseover="keepOpen"
          @mouseout="hide"
        >
          <div
            class="flex text-black-semi p-2 bg-white-catskill"
            dusk="profile-popover-content"
          >
            <img
              @click.stop="showProfile(profileOwner)"
              :id="'user-avatar-' + profileOwner.id"
              :src="avatarPath"
              class="avatar-xl cursor-pointer"
              alt=""
            />
            <div class="pl-2 w-72 flex flex-col space-y-1">
              <h1
                @click.stop="showProfile(profileOwner)"
                class="text-lg hover:underline cursor-pointer"
              >
                {{ profileOwner.name }}
              </h1>
              <p class="text-smaller">macrumors newbie</p>
              <p class="text-smaller">
                <span class="text-gray-lightest mr-1"> Joined: </span
                >{{ profileOwner.join_date }}
              </p>
            </div>
          </div>
          <div class="flex justify-between text-smaller bg-white p-2">
            <div>
              <p class="text-gray-lightest">Messages</p>
              <p class="text-center">{{ profileOwner.profile_posts_count }}</p>
            </div>
            <div>
              <p class="text-gray-lightest">Like Score</p>
              <p class="text-center">{{ profileOwner.received_likes_count }}</p>
            </div>
            <div>
              <p class="text-gray-lightest">Points</p>
              <p class="text-center">0</p>
            </div>
          </div>
          <hr class="text-gray-lighter" />
          <div
            v-if="signedIn && !isAuthUser(this.profileOwner)"
            class="flex bg-white p-2"
          >
            <follow-button
              class="mr-2"
              v-if="signedIn"
              @follow="updateFollow"
              :followed="isFollowed"
              :profileOwner="profileOwner"
            ></follow-button>
            <ignore-user-button
              v-if="signedIn"
              @ignore="updateIgnore"
              :ignored="isIgnored"
              :profile-owner="profileOwner"
            ></ignore-user-button>
            <start-conversation-button
              class="ml-2"
              :user="profileOwner"
            ></start-conversation-button>
          </div>
        </div>
      </template>
    </v-popover>
  </div>
</template>

<script>
import StartConversationButton from "../conversations/StartConversationButton";
import view from "../../mixins/view";
import _ from "lodash";
import authorizable from "../../mixins/authorizable";
import store from "../../store";
export default {
  name: "ProfilePopover",
  components: {
    StartConversationButton,
  },
  props: {
    triggerClasses: {
      type: String,
      default: "",
      required: false,
    },
    triggerText: {
      type: String,
      default: "",
      required: false,
    },
    trigger: {
      type: String,
      default: "username",
      required: false,
    },
    user: {
      type: Object,
      default: {},
      required: true,
    },
    popoverClasses: {
      type: String,
      default: "",
      required: false,
    },
  },
  mixins: [view, authorizable],
  computed: {
    username() {
      return this.triggerText !== ""
        ? this.triggerText
        : this.profileOwner.name;
    },
    isFollowed() {
      return this.profileOwner.followed_by_visitor;
    },
    isIgnored() {
      return this.profileOwner.ignored_by_visitor;
    },
    avatarPath() {
      if (this.isAuthUser(this.user)) {
        return store.state.visitor.avatar_path ?? this.user.avatar_path;
      }
      return this.user.avatar_path;
    },
  },
  data() {
    return {
      profileOwner: this.user,
      isOpen: false,
      isHovering: false,
    };
  },
  watch: {
    profileOwner(newValue, oldValue) {
      store.addOrUpdateProfile(newValue);
    },
  },
  methods: {
    getProfile() {
      return store.getProfile(this.user) ?? this.user;
    },
    updateIgnore(isIgnored) {
      store.updateIgnore(this.profileOwner, isIgnored);
      this.profileOwner.ignored_by_visitor = isIgnored;
    },
    updateFollow(isFollowed) {
      store.updateFollow(this.profileOwner, isFollowed);
      this.profileOwner.followed_by_visitor = isFollowed;
    },
    hovering() {
      this.isHovering = true;
    },
    stopHovering() {
      this.isHovering = false;
    },
    show() {
      this.hovering();
      window.setTimeout(async () => {
        if (this.isHovering) {
          this.stopHovering();
          await this.getData();
          this.openPopover();
        }
      }, 1000);
    },
    async getData() {
      if (store.profileExists(this.user)) {
        this.updateProfile(store.getProfile(this.user));
        return;
      }
      await this.fetchProfile();
    },
    async fetchProfile() {
      try {
        let response = await axios.get("/ajax/profiles/" + this.user.name);
        this.onSuccess(response.data);
      } catch (error) {
        console.log(error);
      }
    },
    updateProfile(user) {
      this.profileOwner = user;
    },
    openPopover() {
      this.isOpen = true;
    },
    keepOpen() {
      this.hovering();
      this.openPopover();
    },
    closePopover() {
      this.isOpen = false;
    },
    hide() {
      this.stopHovering();
      window.setTimeout(() => {
        if (!this.isHovering) {
          this.closePopover();
        }
      }, 300);
    },
    onSuccess(profileOwner) {
      this.updateProfile(profileOwner);
    },
  },
};
</script>

<style>
</style>