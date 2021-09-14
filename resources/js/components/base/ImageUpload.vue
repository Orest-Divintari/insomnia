<template>
  <input
    :disabled="disabled"
    type="file"
    class="text-xs"
    accept="image/*"
    ref="image-upload"
    @change="onChange"
  />
</template>

<script>
export default {
  props: {
    disabled: {
      type: Boolean,
      default: false,
    },
    clearInput: {
      type: Boolean,
      default: false,
    },
  },
  watch: {
    clearInput(newValue, oldValue) {
      if (newValue) {
        this.$refs["image-upload"].value = "";
      }
    },
  },
  methods: {
    onChange(e) {
      if (!e.target.files.length) return;

      // store the image file
      let file = e.target.files[0];

      let reader = new FileReader();

      reader.readAsDataURL(file);

      reader.onload = (e) => {
        this.$emit("loaded", {
          src: e.target.result,
          file: file,
        });
      };
    },
  },
};
</script>

<style lang="scss" scoped>
</style>