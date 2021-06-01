<template>
  <div>
    <p
      v-if="hasNoAbout"
      class="p-7/2 text-sm text-black-semi border border-gray-lighter rounded"
    >
      {{ user.name }} has not provided any additional information
    </p>
    <div>
      <div
        v-if="user.details.about"
        class="border border-gray-lighter p-4 rounded mb-2 text-black-semi text-sm"
      >
        <p v-html="user.details.about"></p>
      </div>
      <div
        v-if="hasPersonalInformation"
        class="border border-gray-lighter p-4 rounded mb-2"
      >
        <div class="flex items-center text-sm mb-1/2" v-if="user.date_of_birth">
          <p class="w-48 text-gray-shuttle">Birthday:</p>
          <p class="text-black-semi">{{ user.date_of_birth }}</p>
        </div>
        <div
          class="flex items-center text-sm mb-1/2"
          v-if="user.details.website"
        >
          <p class="w-48 text-gray-shuttle">Website:</p>
          <p class="text-black-semi">{{ user.details.website }}</p>
        </div>
        <div
          class="flex items-center text-sm mb-1/2"
          v-if="user.details.location"
        >
          <p class="w-48 text-gray-shuttle">Location:</p>
          <p class="text-black-semi">{{ user.details.location }}</p>
        </div>
        <div
          class="flex items-center text-sm mb-1/2"
          v-if="user.details.gender"
        >
          <p class="w-48 text-gray-shuttle">Gender:</p>
          <p class="text-black-semi">{{ user.details.gender }}</p>
        </div>
        <div
          class="flex items-center text-sm mb-1/2"
          v-if="user.details.occupation"
        >
          <p class="w-48 text-gray-shuttle">Occupation:</p>
          <p class="text-black-semi">{{ user.details.occupation }}</p>
        </div>
      </div>
      <div
        class="border border-gray-lighter p-4 rounded mb-2"
        v-if="hasIdentities && can('view_identities', user)"
      >
        <p class="text-black-semi mb-2">Contact</p>
        <div
          class="flex items-center text-sm mb-1/2"
          v-if="user.details.facebook"
        >
          <p class="w-48 text-gray-shuttle">Facebook:</p>
          <p class="text-black-semi">{{ user.details.facebook }}</p>
        </div>
        <div
          class="flex items-center text-sm mb-1/2"
          v-if="user.details.instagram"
        >
          <p class="w-48 text-gray-shuttle">Instagram:</p>
          <p class="text-black-semi">{{ user.details.instagram }}</p>
        </div>
        <div class="flex items-center text-sm mb-1/2" v-if="user.details.skype">
          <p class="w-48 text-gray-shuttle">Skype:</p>
          <p class="text-black-semi">{{ user.details.skype }}</p>
        </div>
        <div
          class="flex items-center text-sm mb-1/2"
          v-if="user.details.google_talk"
        >
          <p class="w-48 text-gray-shuttle">Google talk:</p>
          <p class="text-black-semi">{{ user.details.google_talk }}</p>
        </div>
        <div
          class="flex items-center text-sm mb-1/2"
          v-if="user.details.twitter"
        >
          <p class="w-48 text-gray-shuttle">Twitter:</p>
          <p class="text-black-semi">{{ user.details.twitter }}</p>
        </div>
      </div>
      <follows
        class="mb-2"
        v-if="hasFollowing"
        :profile-owner="profileOwner"
        :dataset="followsDataset"
      ></follows>
      <followed-by
        v-if="hasFollowers"
        :profile-owner="profileOwner"
        :dataset="followedByDataset"
      ></followed-by>
    </div>
  </div>
</template>

<script>
import Follows from "./Follows";
import FollowedBy from "./FollowedBy";
import fetch from "../../mixins/fetch";
import authorizable from "../../mixins/authorizable";
export default {
  components: {
    Follows,
    FollowedBy,
  },
  props: {
    profileOwner: {
      type: Object,
      default: {},
      required: true,
    },
  },
  mixins: [fetch, authorizable],
  data() {
    return {
      user: this.profileOwner,
      followsDataset: {},
      followedByDataset: {},
      hasFollowing: false,
      hasFollowers: false,
    };
  },
  computed: {
    path() {
      return "/ajax/profiles/" + this.profileOwner.name + "/about";
    },
    hasIdentities() {
      let identities = [
        "twitter",
        "instagram",
        "facebook",
        "google_talk",
        "skype",
      ];
      let identityExists = false;
      identities.forEach((identity) => {
        if (this.user.details[identity]) {
          identityExists = true;
        }
      });
      return identityExists;
    },
    hasPersonalInformation() {
      let personalInformation = ["website", "location", "gender", "occupation"];

      let personalInformationExists = false;
      personalInformation.forEach((information) => {
        if (this.user.details[information]) {
          personalInformationExists = true;
        }
      });
      return true;
      return personalInformationExists || this.user.date_of_birth;
    },
    hasNoAbout() {
      let hasDetails = false;

      for (let detail in this.user.details) {
        if (this.user.details[detail]) {
          this.hasDetails = true;
          break;
        }
      }
      return !(this.hasFollowing || this.hasFollowers || this.hasDetails);
    },
  },
  methods: {
    refresh(data) {
      this.followsDataset = data.follows;
      this.followedByDataset = data.followedBy;
      this.user = data.user;
      this.hasFollowing = this.followsDataset.total > 0;
      this.hasFollowers = this.followedByDataset.total > 0;
    },
  },
  created() {
    this.fetchData();
  },
};
</script>

<style lang="scss" scoped>
</style>