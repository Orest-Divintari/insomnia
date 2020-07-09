<template>
  <div>
    <quill-editor
      ref="myTextEditor"
      :disabled="readOnly"
      v-model="content"
      :options="editorOptions"
      required
    ></quill-editor>
  </div>
</template>

<script>
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";
import { Quill, quillEditor } from "vue-quill-editor";
import EventBus from "../eventBus";
import { QuillDeltaToHtmlConverter } from "quill-delta-to-html";

export default {
  props: {
    shouldClear: {
      type: Boolean,
      default: false
    },
    quotedData: {
      default: ""
    },
    styleAttributes: {
      type: String,
      default: ""
    },
    placeholder: {
      type: String,
      default: ""
    },
    readOnly: {
      type: Boolean,
      default: false
    },
    value: {
      type: String,
      default: ""
    }
  },
  components: {
    quillEditor
  },
  data() {
    return {
      content: this.value,
      editorOptions: {
        placeholder: this.placeholder
      }
    };
  },
  methods: {
    clearInput() {
      this.content = "";
    },
    focus() {
      var editor = this.$el.querySelector(".ql-editor");
      editor.focus({
        preventScroll: true
      });
    },
    styleEditor() {
      if (this.styleAttributes) {
        var editor = this.$el.querySelector(".ql-editor");
        editor.classList.add(this.styleAttributes);
      }
    },
    initializeSettings() {
      this.styleEditor();
      this.focus();
    }
  },

  // mounted() {
  //   // var htmlToInsert =
  //   //   "<p class='bg-red-500 text-blue-500'>here is some <strong>awesome</strong> text</p>";
  //   // editor[0].innerHTML = htmlToInsert;
  // },
  watch: {
    content(newValue, oldValue) {
      this.$emit("input", newValue);
    },
    quotedData(newValue, oldValue) {
      var editor = this.$el.querySelector(".ql-editor");
      editor.innerHTML = newValue;
    },
    shouldClear() {
      this.content = "";
    }
    //  <p class='text-blue-500'> <a href='google.com'> this is a link </a> here is some <strong>awesome</strong> text</p>
  },
  mounted() {
    this.$emit("input", this.content);
    EventBus.$on("newReply", this.clearInput);
    this.initializeSettings();
  },
  created() {
    if (this.readOnly) {
      this.editorOptions["theme"] = "bubble";
    }
  }
};
</script>

<style lang="scss">
blockquote {
  @apply .bg-blue-500;
}
</style>