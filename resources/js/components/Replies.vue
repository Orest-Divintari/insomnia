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
  </div>
</template>

<script>
import Reply from "./Reply";
import Paginator from "./Paginator";
export default {
  components: {
    Reply,
    Paginator
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
  }
};
</script>

<style lang="scss" scoped>
</style>