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
    updatePath() {
      return "/api/conversations/" + this.conversation.slug;
    },
    readPath() {
      return "/api/conversations/" + this.conversation.slug + "/read";
    },
    data() {
      return { title: this.title };
    },
    hidePath() {
      return "/api/conversations/" + this.conversation.slug + "/hide";
    },
    leavePath() {
      return "/api/conversations/" + this.conversation.slug + "/leave";
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
    leave() {
      this.hideLeaveModal();
      var path;
      if (this.$refs.hide.checked) {
        path = this.hidePath;
      } else {
        path = this.leavePath;
      }
      axios
        .post(path)
        .then((response) => (window.location.href = "/conversations"))
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
        .post(this.readPath)
        .then((response) => (this.isRead = true))
        .catch((error) => console.log(error));
    },
    markUnread() {
      axios
        .delete(this.readPath)
        .then((response) => (this.isRead = false))
        .catch((error) => console.log(error));
    },
    edit() {
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