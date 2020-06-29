<script>
import SubscribeButton from "./SubscribeButton";
export default {
  components: {
    SubscribeButton
  },
  props: {
    thread: {
      type: Object,
      default: {}
    }
  },
  data() {
    return {
      title: this.thread.title,
      isSubscribed: this.thread.subscribed_by_auth_user,
      editing: false
    };
  },
  computed: {
    path() {
      return "/api/threads/" + this.thread.slug;
    },
    data() {
      return { title: this.title };
    }
  },
  methods: {
    hideDropdown() {
      this.editing = false;
    },
    hideModal() {
      this.$modal.hide("edit-thread");
    },

    edit() {
      this.$modal.show("edit-thread");
    },

    update() {
      axios
        .patch(this.path, this.data)
        .then(response => console.log(response))
        .catch(error => console.log(error));
    }
  }
};
</script>

<style lang="scss" scoped>
</style>