<template>
  <div>
    <input
      @click="isTyping=true"
      v-if="isTyping != true"
      class="input-profile-post"
      :placeholder="placeholder"
    />
    <wysiwyg
      v-if="isTyping"
      v-model="body"
      :style-attributes="'min-h-24'"
      :placeholder="placeholder"
      :shouldClear="posted"
    ></wysiwyg>
    <button @click="post" v-if="isTyping" class="form-button px-4 mt-3">
      <span class="fas fa-reply"></span>
      {{buttoName}}
    </button>
  </div>
</template>

<script>
export default {
  props: {
    placeholder: {
      type: String,
      default: ""
    },
    posted: {
      type: Boolean,
      default: false
    },
    content: {
      type: String,
      default: ""
    },
    buttonName: {
      type: String,
      default: "post"
    }
  },
  data() {
    return {
      body: this.content,
      isTyping: false
    };
  },
  methods: {
    post() {
      this.$emit("posted");
    }
  },
  watch: {
    body() {
      this.$emit("input", this.body);
    }
  }
};
</script>

<style lang="scss" scoped>
</style>