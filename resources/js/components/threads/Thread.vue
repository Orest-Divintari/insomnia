<script>
import SubscribeButton from "../subscription/SubscribeButton";
import LockThreadButton from "./LockThreadButton";
export default {
  components: {
    SubscribeButton,
    LockThreadButton,
  },
  props: {
    thread: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
      title: this.thread.title,
      isSubscribed: this.thread.subscribed_by_auth_user,
      editing: false,
      pinned: this.thread.pinned,
    };
  },
  computed: {
    path() {
      return "/api/threads/" + this.thread.slug;
    },
    data() {
      return { title: this.title };
    },
    sortedByLikes() {
      return window.location.href.includes("?sortByLikes=1");
    },
    pinPath() {
      return this.path + "/pin";
    },
  },
  methods: {
    hideDropdown() {
      this.editing = false;
    },
    hideEditModal() {
      this.$modal.hide("edit-thread");
    },
    edit() {
      this.$modal.show("edit-thread");
    },
    update() {
      axios
        .patch(this.path, this.data)
        .then((response) => this.hideEditModal())
        .catch((error) => showErrorModal(error.response.data));
    },
    togglePin() {
      if (this.thread.pinned) {
        this.unpin();
      } else {
        this.pin();
      }
    },
    pin() {
      axios
        .post(this.pinPath)
        .then(() => (this.pinned = true))
        .catch((error) => console.log(error));
    },
    unpin() {
      axios
        .delete(this.pinPath)
        .then(() => (this.pinned = false))
        .catch((error) => console.log(error));
    },
  },
};
</script>

<style lang="scss" scoped>
</style>