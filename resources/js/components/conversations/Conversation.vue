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
    readPath() {
      return "/api/conversations/" + this.conversation.slug + "/read";
    },
    data() {
      return { title: this.title };
    },
  },
  methods: {
    hideDropdown() {
      this.editing = false;
    },
    hideModal() {
      this.$modal.hide("edit-conversation");
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
        .patch(this.path, this.data)
        .then((response) => console.log(response))
        .catch((error) => console.log(error));
    },
  },
};
</script>

<style lang="scss" scoped>
</style>