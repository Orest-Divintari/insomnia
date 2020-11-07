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
      editing: false,
    };
  },
  computed: {
    path() {
      return "/api/conversations/" + this.conversation.slug;
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
      this.$modal.hide("edit-thread");
    },

    edit() {
      this.$modal.show("edit-thread");
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