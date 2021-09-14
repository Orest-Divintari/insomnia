<script>
import SubscribeButton from "../subscription/SubscribeButton";
import LockThreadButton from "./LockThreadButton";
import PinThreadButton from "./PinThreadButton";
import Replies from "../replies/Replies";
export default {
  name: "Thread",
  components: {
    Replies,
    SubscribeButton,
    LockThreadButton,
    PinThreadButton,
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
      editing: false,
    };
  },
  computed: {
    path() {
      return "/ajax/threads/" + this.thread.slug;
    },
    data() {
      return { title: this.title };
    },
    sortedByLikes() {
      return window.location.href.includes("?sort_by_likes=1");
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
  },
};
</script>

<style lang="scss" scoped>
</style>