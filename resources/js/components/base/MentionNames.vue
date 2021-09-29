
<template>
  <div></div>
</template>

<script>
import Tribute from "tributejs";
export default {
  methods: {
    remoteSearch(name, cb) {
      if (name.length >= 2) {
        axios
          .get("/ajax/search/names/" + name)
          .then(({ data }) => {
            let usernames = [];
            data.forEach((name) => {
              usernames.push({ key: name, value: name });
            });
            cb(usernames);
          })
          .catch((error) => cb([]));
      }
    },
    attachTribute() {
      var tribute = new Tribute({
        values: (text, cb) => {
          this.remoteSearch(text, (users) => cb(users));
        },
        noMatchTemplate: function () {
          return '<span style:"visibility: hidden;"></span>';
        },
        menuItemLimit: 5,
        containerClass:
          "mention-container shadow-xl w-48 rounded bg-opacity-1 bg-white scrolling-auto ",
        itemClass:
          "hover:bg-blue-lighter rounded p-2 pr-4 text-sm cursor-pointer",
      });
      tribute.attach(document.querySelector(".ql-editor"));
    },
  },
  mounted() {
    this.attachTribute();
  },
};
</script>

<style lang="scss" scoped>
.mention-container li span {
  font-weight: bold;
}
</style>
