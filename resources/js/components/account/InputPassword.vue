<template>
  <div class="flex">
    <input
      :type="type"
      :name="name"
      class="bg-white form-input border-r-0 rounded-r-none"
      v-model="password"
      required
    />
    <div
      v-if="!visible"
      @click="showPassword"
      class="border border-white-catskill rounded-r flex items-center p-2 text-gray-700 hover:text-black-semi"
    >
      <i class="fas fa-eye mr-1 cursor-pointer"></i
      ><button type="button" class="focus:outline-none text-xs">Show</button>
    </div>
    <div
      v-else
      @click="hidePassword"
      class="border border-white-catskill rounded-r flex items-center p-2 text-gray-700 hover:text-black-semi"
    >
      <i class="fas fa-eye-slash mr-1 cursor-pointer"></i
      ><button type="button" class="focus:outline-none text-xs">Hide</button>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    name: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      password: "",
      visible: false,
    };
  },
  computed: {
    type() {
      return this.visible ? "text" : "password";
    },
  },
  methods: {
    showPassword() {
      this.visible = true;
    },
    hidePassword() {
      this.visible = false;
    },
  },
  watch: {
    password(newValue, oldValue) {
      this.$emit("input", newValue);
    },
  },
};
</script>

<style lang="scss" scoped>
</style>