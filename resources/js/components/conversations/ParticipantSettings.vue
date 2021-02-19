<template>
  <div>
    <dropdown :hideOnClick="true">
      <template v-slot:dropdown-trigger>
        <button
          class="hover:bg-blue-lighter focus:outline-none focus:bg-blue-lighter focus:text-black focus:shadow-md text-gray-lightest hover:text-black rounded-full px-2 pt-1/2 pb-2"
        >
          ...
        </button>
      </template>
      <template v-slot:dropdown-items>
        <ul class="bg-white cursor-pointer">
          <li
            @click="removeAsAdmin"
            v-if="isAdmin"
            class="p-2 hover:bg-blue-lighter"
          >
            <i class="fas fa-users-cog pr-2"></i> Remove as admin
          </li>
          <li @click="setAsAdmin" v-else class="p-2 hover:bg-blue-lighter">
            <i class="fas fa-users-cog pr-2"></i> Set as admin
          </li>
          <li
            v-if="!authorize('is', participant)"
            @click="removeParticipant"
            class="p-2 hover:bg-blue-lighter"
          >
            <i class="fas fa-user-times pr-2"></i> Remove
          </li>
        </ul>
      </template>
    </dropdown>
  </div>
</template>

<script>
export default {
  props: {
    participant: {
      type: Object,
      default: {},
    },
    conversation: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
      isAdmin: this.participant.conversation_admin,
      isOpen: true,
    };
  },
  computed: {
    participantPath() {
      return (
        "/api/conversations/" +
        this.conversation.slug +
        "/participants/" +
        this.participant.id
      );
    },
    adminPath() {
      return (
        "/api/conversations/" +
        this.conversation.slug +
        "participants/" +
        this.participant.id +
        "/admin"
      );
    },
  },
  methods: {
    removeAsAdmin() {
      axios
        .delete(this.adminPath)
        .then(() => this.onSuccess())
        .catch();
    },
    setAsAdmin() {
      axios
        .patch(this.adminPath)
        .then(() => this.onSuccess())
        .catch();
    },
    removeParticipant() {
      axios
        .delete(this.participantPath)
        .then(() => location.reload())
        .catch((error) => console.log(error));
    },
    toggleAdmin() {
      this.isAdmin = !this.isAdmin;
    },
    onSuccess() {
      this.toggleAdmin();
    },
  },
};
</script>

<style lang="scss" scoped>
</style>
