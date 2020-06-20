<template>
  <div class="mt-5">
    <div>
      <button class="btn-paginator" v-show="true">
        <span class="fas fa-caret-left text-xs"></span>
        Prev
      </button>

      <button
        @click.prevent="changePage(pageNumber)"
        v-for="(pageNumber, key) in pages"
        class="btn-paginator mx-1"
      >{{ pageNumber }}</button>

      <button class="btn-paginator" v-show="nextPage">
        Next
        <span class="fas fa-caret-right text-xs"></span>
      </button>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    dataset: Object
  },
  data() {
    return {
      nextPage: false,
      previousPage: false,
      lastPage: false,
      currentPage: false,
      pages: []
    };
  },
  methods: {
    initialize() {
      (this.nextPage = this.dataset.next_page_url),
        (this.previousPage = this.dataset.prev_page_url),
        (this.lastPage = this.dataset.last_page_url),
        (this.currentPage = this.dataset.current_page);
    },
    changePage(pageNumber) {
      this.$emit("changePage", pageNumber);
      this.updateUrl(pageNumber);
    },
    updateUrl(pageNumber) {
      history.pushState(null, null, "?page=" + pageNumber);
    },
    computePageRange() {
      for (
        let pageCount = 1;
        pageCount <= this.dataset.last_page;
        pageCount++
      ) {
        this.pages.push(pageCount);
      }
    }
  },
  created() {
    this.initialize();
    this.computePageRange();
  }
};
</script>

<style lang="scss" scoped>
</style>