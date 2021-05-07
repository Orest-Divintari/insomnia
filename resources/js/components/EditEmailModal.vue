<template>
  <div>
    <button type="button" class="btn-white-blue" @click="show">change</button>
    <modal @closed="hide" width="800px" height="auto" name="edit-email">
      <div class="flex justify-between items-center p-3 bg-white-catskill">
        <p class="text-lg text-black-semi">Change email</p>
        <button
          type="button"
          @click="hide"
          class="fas fa-times text-lg"
        ></button>
      </div>
      <form class="form-container">
        <!-- ROW -->
        <div class="form-row">
          <!-- LEFT -->
          <div class="form-left-col">
            <label class="form-label" for="email">Email:</label>
          </div>
          <!-- RIGHT -->
          <div class="form-right-col">
            <p class="form-label-phone">Email:</p>
            <div>
              <input
                v-focus
                type="email"
                name="email"
                class="bg-white form-input"
                v-model="form.email"
                ref="email"
              />
            </div>
          </div>
        </div>
        <div class="form-row">
          <!-- LEFT -->
          <div class="form-left-col">
            <label class="form-label" for="password">Current password:</label>
          </div>
          <!-- RIGHT -->
          <div class="form-right-col">
            <p class="form-label-phone">Current password:</p>
            <input-password @input="sync" name="password"></input-password>
          </div>
        </div>
        <div class="form-button-container justify-center">
          <button @click="save" type="button" class="form-button">Save</button>
        </div>
      </form>
    </modal>
  </div>
</template>

<script>
export default {
  props: {
    user: {
      type: Object,
      default: {},
      required: true,
    },
  },
  data() {
    return {
      form: {
        password: "",
        email: this.user.email,
      },
    };
  },
  computed: {
    path() {
      return "/ajax/users/" + this.user.name + "/email";
    },
  },
  methods: {
    sync(input) {
      this.form.password = input;
    },
    show() {
      this.$modal.show("edit-email");
    },
    hide() {
      this.$modal.hide("edit-email");
    },
    save() {
      axios
        .patch(this.path, this.form)
        .then(() => this.onSave())
        .catch((error) => showErrorModal(error.response.data));
    },
    onSave() {
      this.hide();
      location.reload();
    },
  },
};
</script>

<style lang="scss" scoped>
</style>