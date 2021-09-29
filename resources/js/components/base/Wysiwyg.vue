<template>
  <div class="bg-white relative" dusk="wysiwyg-component">
    <input :value="content" :name="name" type="hidden" />
    <quill-editor
      ref="myTextEditor"
      :disabled="readOnly"
      v-model="content"
      :options="editorOptions"
      required
    ></quill-editor>
    <mention-names v-if="mentionNames"> </mention-names>
  </div>
</template>

<script>
import Parchment from "parchment";
import MentionNames from "../base/MentionNames";
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";
import { Quill, quillEditor } from "vue-quill-editor";
import EventBus from "../../eventBus";
import { QuillDeltaToHtmlConverter } from "quill-delta-to-html";

export default {
  name: "Wysiwyg",
  components: {
    MentionNames,
    quillEditor,
  },
  props: {
    mentionNames: {
      type: Boolean,
      default: true,
    },
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
    appendSuggestion(suggestion) {
      this.content = suggestion;
    },
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

      console.log(this.editor);
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
    this.editor.setAttribute("id", "input-reply-wysiwyg");
    this.editor.setAttribute("dusk", "input-reply-wysiwyg");
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
.ql-editor a {
  color: red;
}
</style>