<template>
  <div class="mt-6">
    <div class="reply-container">
      <div class="reply-left-col">
        <img :src="user.avatar_path" class="avatar-xl" alt />
      </div>
      <div class="w-full p-3">
        <form @submit.prevent="post">
          <wysiwyg
            v-model="body"
            :style-attributes="'reply-form'"
            placeholder="Write your reply..."
            :quoted-data="quotedData"
            :shouldClear="posted"
          ></wysiwyg>
          <button type="submit" class="mt-4 form-button">Post Reply</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import EventBus from "../eventBus";
export default {
  components: {},
  data() {
    return {
      body: "",
      quotedData: "",
      posted: false
    };
  },
  computed: {
    path() {
      return "/api" + window.location.pathname + "/replies";
    }
  },
  methods: {
    post() {
      axios
        .post(this.path, { body: this.body })
        .then(({ data }) => this.addReply(data))
        .catch(error => console.log(error.response));
    },
    addReply(data) {
      this.posted = true;
      // EventBus.$emit("newReply", data);
      this.body = "";
    }
  },
  mounted() {
    EventBus.$on("quotedReply", quotedData => {
      this.quotedData = quotedData;
      // var editor = this.$children.querySelector(".ql-editor");
      // console.log(this.$refs);
      // editor.innerHTML = quotedData;
      //   // var htmlToInsert =
      //   //   "<p class='bg-red-500 text-blue-500'>here is some <strong>awesome</strong> text</p>";
      //   // editor[0].innerHTML = htmlToInsert;
      // },
      // var element = document.querySelector("trix-editor");
      // // '<blockquote class="blockquote"> <div class="container"> <div class="title">ola anthira</div> <div>ola kala</div> </div></blockquote>';
      // element.editor.insertHTML("<blockquote> 5");
      // element.editor.insertLineBreak();
      // element.editor.insertHTML("<a href='google.com> 3 </a>");
      // element.editor.insertLineBreak();
      // var newDiv = document.createElement("div");
      // var newText = document.createTextNode("hello modasafka");
      // newDiv.appendChild(newText);
      // newDiv.classList.add("bg-red-500");
      // element.appendChild(newDiv);
      // // element.innertHTML =
      // //   "<p> this is some <strong> strong </strong> text</p>";
    });
  }
};
</script>

<style lang="scss" scoped>
</style>