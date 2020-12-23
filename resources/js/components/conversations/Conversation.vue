<script>
import InviteParticipantsModal from "./InviteParticipantsModal";
import ParticipantSettings from "../conversations/ParticipantSettings";
import view from "../../mixins/view";
import EventBus from "../../eventBus";
export default {
  components: {
    InviteParticipantsModal,
    ParticipantSettings,
  },
  props: {
    conversation: {
      type: Object,
      default: {},
    },
  },
  mixins: [view],
  data() {
    return {
      title: this.conversation.title,
      isRead: !this.conversation.has_been_updated,
      locked: this.conversation.locked,
      starred: this.conversation.starred,
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
    starPath() {
      return this.path + "/star";
    },
    form() {
      return { title: this.title, locked: this.$refs.lock.checked };
    },
    hidePath() {
      return this.path + "/hide";
    },
    leavePath() {
      return this.path + "/leave";
    },
  },
  methods: {
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
      var path = this.leaveActionPath();
      axios
        .patch(path)
        .then(() => this.viewConversationList())
        .catch((error) => console.log(error));
    },
    leaveActionPath() {
      if (this.$refs.hide.checked) {
        return this.hidePath;
      } else if (this.$refs.leave.checked) {
        return this.leavePath;
      }
    },
    toggleRead() {
      if (this.isRead) {
        this.markUnread();
      } else {
        this.markRead();
      }
    },
    star() {
      axios
        .post(this.starPath)
        .then(() => (this.starred = true))
        .then((error) => console.log(error));
    },
    unstar() {
      axios
        .delete(this.starPath)
        .then(() => (this.starred = false))
        .then((error) => console.log(error));
    },
    toggleStar() {
      if (this.starred) {
        this.unstar();
      } else {
        this.star();
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
    showEditModal() {
      this.$modal.show("edit-conversation");
    },
    update() {
      axios
        .patch(this.updatePath, this.form)
        .then(() => this.onSuccess())
        .catch((error) => showErrorModal(error.response.data));
    },
    onSuccess() {
      this.locked = !this.locked;
      this.hideEditModal();
      EventBus.$emit("lock-repliable", this.$refs.lock.checked);
    },
  },
};
</script>

<style lang="scss" scoped>
</style>