<script>
export default {
  props: {
    conversation: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
      title: this.conversation.title,
      isRead: !this.conversation.has_been_updated,
      editing: false,
    };
  },
  computed: {
    path() {
      return "/api/conversations/" + this.conversation.slug;
    },
    updatePath() {
      return this.path;
    },
    readPath() {
      return this.path + "/read";
    },
    data() {
      return { title: this.title };
    },
    hidePath() {
      return this.path + "/hide";
    },
    leavePath() {
      return this.path + "/leave";
    },
  },
  methods: {
    hideDropdown() {
      this.editing = false;
    },
    hideEditModal() {
      this.$modal.hide("edit-conversation");
    },
    hideLeaveModal() {
      this.$modal.hide("leave-conversation");
    },
    showLeaveModal() {
      this.$modal.show("leave-conversation");
    },
    viewConversationList() {
      window.location.href = "/conversations";
    },
    leave() {
      this.hideLeaveModal();
      var path;
      if (this.$refs.hide.checked) {
        path = this.hidePath;
      } else if (this.$refs.leave.checked) {
        path = this.leavePath;
      }
      axios
        .patch(path)
        .then((response) => this.viewConversationList())
        .catch((error) => console.log(error));
    },
    toggleRead() {
      if (this.isRead) {
        this.markUnread();
      } else {
        this.markRead();
      }
    },
    markRead() {
      axios
        .patch(this.readPath)
        .then((response) => (this.isRead = true))
        .catch((error) => console.log(error));
    },
    markUnread() {
      axios
        .delete(this.readPath)
        .then((response) => (this.isRead = false))
        .catch((error) => console.log(error));
    },
    showEditModal() {
      this.$modal.show("edit-conversation");
    },
    update() {
      axios
        .patch(this.updatePath, this.data)
        .then((response) => console.log(response))
        .catch((error) => console.log(error.response.errors));
    },
  },
};
</script>

<style lang="scss" scoped>
</style>