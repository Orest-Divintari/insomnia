<template>
  <div class="sidebar-block p-0 border border-t-0 border-blue-light">
    <p
      dusk="invite-participants-button"
      class="p-2 text-sm bg-gray-lighter text-right blue-link"
      @click="showModal"
    >
      Invite more
    </p>
    <modal name="invite-participants-modal" height="auto" width="50%">
      <div dusk="invite-participants-modal" class="form-container">
        <div
          class="
            flex
            justify-between
            items-center
            bg-blue-light
            text-lg text-black-semi
            border-b border-blue-light
            py-3
            px-3
          "
        >
          <p>Invite members to conversation</p>
          <button @click="hideModal" class="fas fa-times"></button>
        </div>
        <form>
          <!-- ROW -->
          <div class="form-row">
            <!-- LEFT -->
            <div class="form-left-col">
              <label class="form-label" for="invite-members"
                >Invite members:</label
              >
            </div>
            <!-- RIGHT -->
            <div class="form-right-col">
              <p class="form-label-phone">Invite members:</p>
              <div>
                <names-autocomplete-input
                  input-name="participants"
                  v-model="participants"
                ></names-autocomplete-input>
                <p class="text-gray-lightest text-xs mt-2">
                  You may enter multiple names here separated by comma. Invited
                  members will be able to see the entire conversation from the
                  beginning.
                </p>
              </div>
            </div>
          </div>
          <div class="form-button-container justify-center">
            <button
              dusk="invite-participants-submit"
              @click="invite"
              type="button"
              class="form-button"
            >
              Invite
            </button>
          </div>
        </form>
      </div>
    </modal>
  </div>
</template>

<script>
export default {
  data() {
    return {
      participants: "",
      conversation: this.$parent.conversation,
    };
  },
  computed: {
    path() {
      return "/ajax/conversations/" + this.conversation.slug + "/participants";
    },
    data() {
      return { participants: this.participants };
    },
  },
  methods: {
    hideModal() {
      this.$modal.hide("invite-participants-modal");
      this.participants = "";
    },
    showModal() {
      this.$modal.show("invite-participants-modal");
    },
    invite() {
      axios
        .post(this.path, this.data)
        .then((response) => this.onSuccess())
        .catch((error) => showErrorModal(error.response.data));
    },
    onSuccess() {
      location.reload();
    },
  },
};
</script>

<style lang="scss" scoped>
</style>