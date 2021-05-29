<template>
  <div @click="fetchData" @dblclick="showProfile(user)">
    <dropdown styleClasses="mt-0 w-84">
      <template v-slot:dropdown-trigger>
        <a class="flex items-center head-tab-item">
          <img :src="avatarPath" class="avatar-sm mr-1" alt="" />
          <button class="focus:outline-none">{{ user.name }}</button>
        </a>
      </template>
      <template v-slot:dropdown-items>
        <div v-if="fetchedData" class="dropdown-title p-3">Your account</div>
        <div v-if="!fetchedData" class="dropdown-title p-3">Loading...</div>
        <div v-if="fetchedData" class="bg-blue-lighter">
          <div class="p-2">
            <div class="flex">
              <avatar
                :user="user"
                buttonClasses="w-12 h-6 text-xs"
                avatarClasses="w-12 h-12 rounded-full"
              ></avatar>
              <div class="pl-4">
                <a
                  @click="showProfile(user)"
                  class="text-md blue-link cursor-pointer font-semibold"
                  v-text="user.name"
                >
                </a>
                <p class="text-smaller text-black-semi">Macrumors newbie</p>
              </div>
            </div>
            <div class="text-smaller text-gray-shuttle mt-1">
              <div class="flex leading-relaxed">
                <p class="flex-1">Messages:</p>
                <p v-text="user.messages_count"></p>
              </div>
              <div class="flex leading-relaxed">
                <p class="flex-1">Likes score:</p>
                <p v-text="user.received_likes_count"></p>
              </div>
              <div class="flex leading-relaxed">
                <p class="flex-1">Points:</p>
                <p>0</p>
              </div>
            </div>
          </div>
          <hr class="border-t-1 border-gray-lighter" />
          <div class="flex text-black-semi text-smaller">
            <div class="w-1/2 border-r border-gray-lighter">
              <div class="flex flex-col">
                <a
                  class="p-2 border-white border-t hover:bg-white-catskill cursor-pointer hover:text-blue-mid"
                  :href="'/search?posted_by=' + user.name"
                  >Your Content
                </a>
                <hr class="border-b-1 border-gray-lighter" />
                <a
                  class="p-2 border-white border-t hover:bg-white-catskill cursor-pointer hover:text-blue-mid"
                  href="/account/likes"
                  >Likes Received</a
                >
                <hr class="border-b-1 border-gray-lighter" />
                <a
                  class="p-2 border-white border-t hover:bg-white-catskill cursor-pointer hover:text-blue-mid"
                  href="/account/details"
                  >Account details
                </a>
                <hr class="border-b-1 border-gray-lighter" />
                <a
                  class="p-2 border-white border-t hover:bg-white-catskill cursor-pointer hover:text-blue-mid"
                  href="/account/password"
                  >Password and security</a
                >
                <hr class="border-b-1 border-gray-lighter" />
                <a
                  class="p-2 border-white border-t hover:bg-white-catskill cursor-pointer hover:text-blue-mid"
                  href="/account/privacy"
                  >Privacy</a
                >
                <hr class="border-b-1 border-gray-lighter" />
              </div>
            </div>
            <div class="w-1/2 border-t-1 border-white flex flex-col">
              <a
                class="p-2 border-white border-t hover:bg-white-catskill cursor-pointer hover:text-blue-mid"
                href="/account/follows"
                >Following
              </a>
              <hr class="border-b-1 border-gray-lighter" />
              <a
                class="p-2 border-white border-t hover:bg-white-catskill cursor-pointer hover:text-blue-mid"
                href="/account/ignored"
                >Ignoring
              </a>
              <hr class="border-b-1 border-gray-lighter" />
              <a
                class="p-2 border-white border-t hover:bg-white-catskill cursor-pointer hover:text-blue-mid"
                href="/account/preferences"
                >Preferences
              </a>
              <hr class="border-b-1 border-gray-lighter" />
              <a
                class="p-2 border-white border-t hover:bg-white-catskill cursor-pointer hover:text-blue-mid"
                :href="'/search?posted_by=' + user.name"
                >Signature</a
              >
              <hr class="border-b-1 border-gray-lighter" />
              <a
                class="p-2 border-white border-t hover:bg-white-catskill cursor-pointer hover:text-blue-mid"
                :href="'/search?posted_by=' + user.name"
                >Connected accounts</a
              >
              <hr class="border-b-1 border-gray-lighter" />
            </div>
          </div>
          <form action="/logout" method="POST" class="flex">
            <button
              class="flex-1 p-2 text-black-semi text-smaller border-white border-t hover:bg-white-catskill cursor-pointer hover:text-blue-mid"
              :href="'/search?posted_by=' + user.name"
            >
              Logout
            </button>
          </form>
        </div>
      </template>
    </dropdown>
  </div>
</template>

<script>
import store from "../../store";
import Dropdown from "../Dropdown.vue";
import authorizable from "../../mixins/authorizable";
import Avatar from "../profile/Avatar";
import view from "../../mixins/view";
import fetch from "../../mixins/fetch";
export default {
  components: {
    Avatar,
    Dropdown,
  },
  props: {
    profileOwner: {
      type: Object,
      default: {},
      required: true,
    },
  },
  mixins: [authorizable, view, fetch],
  computed: {
    avatarPath() {
      return this.state.visitor.avatar_path ?? this.user.avatar_path;
    },
    canUpdate() {
      return this.hover && this.ownsProfile(this.user);
    },
    path() {
      return "/ajax/profiles/" + this.user.name;
    },
  },
  data() {
    return {
      fetchedData: false,
      user: this.profileOwner,
      state: store.state,
      hover: false,
    };
  },
  methods: {
    refresh(user) {
      this.user = user;
      this.fetchedData = true;
    },
  },
};
</script>

<style lang="scss" scoped>
.edit-background-gradient {
  background-image: linear-gradient(
    to bottom,
    rgba(0, 0, 0, 0) 60%,
    rgba(0, 0, 0, 0.9) 100%
  );
}
</style>