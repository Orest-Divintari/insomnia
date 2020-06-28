<template>
  <div :class="{'-mt-12' : isPaginated}">
    <paginator
      :class="{'pb-5': isPaginated}"
      @isPaginated="isPaginated=true"
      @changePage="fetchData"
      :dataset="dataset"
    ></paginator>
    <reply
      v-for="(reply, index) in items"
      :key="reply.id"
      :index="index"
      :reply="reply"
      :threadPoster="thread.poster.name"
    ></reply>
    <paginator @isPaginated="isPaginated=true" @changePage="fetchData" :dataset="dataset"></paginator>
    <reply-form v-if="signedIn"></reply-form>
    <p v-else class="text-xs mt-4 text-center">
      You must
      <a href="/login" class="text-blue-mid underline">sign in</a> or
      <a href="/register" class="text-blue-mid underline">register</a> to reply here.
    </p>
  </div>
</template>

<script>
import Reply from "./Reply";
import Paginator from "./Paginator";
import ReplyForm from "./ReplyForm";
import EventBus from "../eventBus";
export default {
  components: {
    Reply,
    Paginator,
    ReplyForm
  },
  props: {
    thread: {
      type: Object,
      default: {}
    }
  },
  data() {
    return {
      isPaginated: false,
      items: [],
      dataset: {}
    };
  },
  methods: {
    add(item) {
      this.items.push(item);
    },
    endpoint(pageNumber) {
      var path = "/api/threads/" + this.thread.slug + "/replies";
      if (!pageNumber) {
        return path;
      }
      return path + "?page=" + pageNumber;
    },
    updateData(data) {
      this.items = data.data;
      this.dataset = data;
    },
    refresh({ data }) {
      this.updateData(data);
      window.scrollTo(0, 0);
    },
    fetchData(pageNumber) {
      axios
        .get(this.endpoint(pageNumber))
        .then(response => this.refresh(response))
        .catch(error => console.log(error.response));
    }
  },
  created() {
    this.fetchData();
  },
  mounted() {
    EventBus.$on("newReply", this.add);
  }
};
</script>

<style lang="scss" scoped>
</style>