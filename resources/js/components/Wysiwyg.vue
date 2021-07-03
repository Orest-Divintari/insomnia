<template>
  <div class="bg-white">
    <input :value="content" :name="name" type="hidden" />
    <quill-editor
      ref="myTextEditor"
      :disabled="readOnly"
      v-model="content"
      :options="editorOptions"
      name="mokocomak"
      required
    ></quill-editor>
  </div>
</template>

<script>
import Parchment from "parchment";

import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";
import { Quill, quillEditor } from "vue-quill-editor";
import EventBus from "../eventBus";
import { QuillDeltaToHtmlConverter } from "quill-delta-to-html";

export default {
  props: {
    name: {
      type: String,
      default: "",
    },
    shouldClear: {
      type: Boolean,
      default: false,
    },
    quotedData: {
      default: "",
    },
    styleAttributes: {
      type: String,
      default: "",
    },
    placeholder: {
      type: String,
      default: "",
    },
    readOnly: {
      type: Boolean,
      default: false,
    },
    value: {
      type: String,
      default: "",
    },
  },
  components: {
    quillEditor,
  },
  data() {
    return {
      content: this.value,
      editorOptions: {
        placeholder: this.placeholder,
      },
      editor: {},
    };
  },
  methods: {
    clearInput() {
      this.content = "";
    },
    focus() {
      this.editor.focus({
        preventScroll: true,
      });
    },
    styleEditor() {
      if (this.styleAttributes) {
        this.editor.classList.add(this.styleAttributes);
      }
    },
    initializeSettings() {
      this.styleEditor();
      this.focus();
    },
  },

  watch: {
    content(newValue, oldValue) {
      this.$emit("input", newValue);
    },
    quotedData(newValue, oldValue) {
      this.editor.innerHTML = newValue;
      this.focus();
    },
    shouldClear() {
      this.content = "";
    },
  },
  mounted() {
    this.editor = this.$refs.myTextEditor.$el.querySelector(".ql-editor");
    this.$emit("input", this.content);
    EventBus.$on("newReply", this.clearInput);
    this.initializeSettings();
  },
  created() {
    if (this.readOnly) {
      this.editorOptions["theme"] = "bubble";
    }
  },
};
</script>

<style lang="scss" >
</style>