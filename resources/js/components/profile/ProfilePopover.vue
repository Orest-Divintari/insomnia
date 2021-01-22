<template>
  <div>
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
        @click="showProfile(user)"
        class="cursor-pointer"
        @mouseover="show"
        @mouseout="hide"
      >
        <img
          v-if="trigger == 'avatar'"
          :src="user.avatar_path"
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
          @mouseover="show"
          @mouseout="hide"
        >
          <div class="flex text-black-semi p-2 bg-white-catskill">
            <img
              @click="showProfile(user)"
              :src="user.avatar_path"
              class="avatar-xl cursor-pointer"
              alt=""
            />
            <div class="pl-2 w-72 flex flex-col space-y-1">
              <h1
                @click="showProfile(user)"
                class="text-lg hover:underline cursor-pointer"
              >
                {{ user.name }}
              </h1>
              <p class="text-smaller">macrumors newbie</p>
              <p class="text-smaller">
                <span class="text-gray-lightest mr-1"> Joined: </span
                >{{ user.join_date }}
              </p>
            </div>
          </div>
          <div class="flex justify-between text-smaller bg-white p-2">
            <div>
              <p class="text-gray-lightest">Messages</p>
              <p class="text-center">{{ user.message_count }}</p>
            </div>
            <div>
              <p class="text-gray-lightest">Like Score</p>
              <p class="text-center">{{ user.like_score }}</p>
            </div>
            <div>
              <p class="text-gray-lightest">Points</p>
              <p class="text-center">0</p>
            </div>
          </div>
          <hr class="text-gray-lighter" />
          <div v-if="!isAuthUser(this.user)" class="flex bg-white p-2">
            <follow-button
              @follow="updateFollow"
              :is-followed-by-auth-user="isFollowed"
              :profileOwner="user"
            ></follow-button>
            <p class="ml-2 btn-white-blue">Ignore</p>
            <start-conversation-button
              class="ml-2"
              :user="user"
            ></start-conversation-button>
          </div>
        </div>
      </template>
    </v-popover>
  </div>
</template>

<script>
import FollowButton from "../profile/FollowButton";
import view from "../../mixins/view";
import _ from "lodash";
import authorization from "../../mixins/authorization";
import { store } from "../../store";
export default {
  components: {
    FollowButton,
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
  mixins: [view, authorization],
  computed: {
    username() {
      return this.triggerText !== "" ? this.triggerText : this.user.name;
    },
  },
  data() {
    return {
      isOpen: false,
      isFollowed: false,
      isHovered: false,
    };
  },
  methods: {
    updateFollow(isFollowed) {
      store.updateFollow(this.user, isFollowed);
      this.isFollowed = isFollowed;
    },
    show() {
      this.isHovered = true;
      window.setTimeout(() => {
        if (this.isHovered) {
          this.openPopover();
        }
      }, 1000);
    },
    async openPopover() {
      await this.getIsFollowed();
      this.isOpen = true;
    },
    closePopover() {
      this.isOpen = false;
    },
    hide() {
      this.isHovered = false;
      window.setTimeout(() => {
        if (!this.isHovered) {
          this.closePopover();
        }
      }, 300);
    },
    async getIsFollowed() {
      if (this.isOpen) {
        return;
      }
      if (this.isFollowInStore()) {
        this.isFollowed = store.isFollowing(this.user);
        return;
      }
      await this.isFollowedByAuthUser();
    },
    isFollowInStore() {
      return store.followExists(this.user);
    },
    async isFollowedByAuthUser() {
      try {
        let response = await axios.get(
          "/api/users/" + this.user.name + "/isFollowedByAuthUser"
        );
        this.onSuccess(response.data);
      } catch (error) {
        console.log(error);
      }
    },
    onSuccess(data) {
      this.isFollowed = data.is_followed;
      store.updateFollow(this.user, data.is_followed);
    },
  },
};
</script>

<style>
</style>