<template>
  <div class="mt-5">
    <div>
      <div v-show="shouldPaginate">
        <div class="flex">
          <button
            @click="changePage(--currentPage)"
            class="btn-paginator"
            v-show="this.previousPageUrl"
          >
            <span class="fas fa-caret-left text-xs"></span>
            Prev
          </button>

          <template v-for="(page, key) in pages">
            <div v-if="page == '...'">
              <v-popover
                offset="10"
                popoverArrowClass="''"
                popoverInnerClass="''"
              >
                <button class="btn-paginator cursor-pointer">...</button>
                <template slot="popover">
                  <div
                    class="absolute -ml-4 bg-blue-lighter shadow-2xl border border-blue-light rounded"
                  >
                    <p class="p-2 bg-white border-b border-blue-light rounded">
                      Go to page
                    </p>
                    <div class="flex items-center p-2">
                      <input
                        type="text"
                        class="p-1 m-1 rounded border border-bluelight focus:outline-none"
                        placeholder="Page"
                        v-model="goToPage"
                      />
                      <button
                        @click="changePage(goToPage)"
                        class="text-blue-mid p-1 focus:outline-none"
                      >
                        Go
                      </button>
                    </div>
                  </div>
                </template>
              </v-popover>
            </div>
            <div
              v-else
              @click.prevent="changePage(page)"
              class="btn-paginator cursor-pointer"
              :class="{ 'bg-blue-mid text-white': page == currentPage }"
            >
              {{ page }}
            </div>
          </template>

          <button
            @click="changePage(++currentPage)"
            class="btn-paginator"
            v-show="nextPageUrl"
          >
            Next
            <span class="fas fa-caret-right text-xs"></span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import EventBus from "../eventBus";
export default {
  props: {
    dataset: Object,
  },
  data() {
    return {
      goToPage: "",
      firstPageUrl: false,
      nextPageUrl: false,
      previousPageUrl: false,
      lastPageUrl: false,
      firstPage: 1,
      lastPage: 0,
      currentPage: false,
      pages: [],
    };
  },
  methods: {
    initialize() {
      this.currentPage = this.dataset.current_page;
      this.firstPageUrl = this.dataset.first_page_url;
      this.lastPageUrl = this.dataset.last_page_url;
      this.nextPageUrl = this.dataset.next_page_url;
      this.previousPageUrl = this.dataset.prev_page_url;
      this.lastPage = this.dataset.last_page;
    },
    validatePageNumber(page) {
      let pageNumber = parseInt(page);
      if (Number.isInteger(pageNumber)) {
        if (pageNumber < this.firstPage) {
          pageNumber = this.firstPage;
        } else if (pageNumber > this.lastPage) {
          pageNumber = this.lastPage;
        }
        return parseInt(pageNumber);
      }
      return null;
    },
    brodcast(page) {
      this.$emit("changePage", page);
    },
    clearInput() {
      this.goToPage = "";
    },
    changePage(page) {
      this.clearInput();
      var pageNumber = this.validatePageNumber(page);
      if (pageNumber) {
        var path = window.location.pathname + "?page=" + page;
        window.location.href = path;
      }
    },
    startFromPage() {
      var from = 0;

      if ((this.lastPage >= 6 && this.currentPage < 6) || this.lastPage < 6) {
        from = 2;
      } else if (this.currentPage >= 6) {
        from = this.currentPage - 2;
      } else {
        from = this.currentPage + 1;
      }
      return from;
    },
    upToPage() {
      var range = 0;

      if (this.lastPage - this.currentPage > 2) {
        range = this.currentPage + 2;
      } else {
        range = this.lastPage - 1;
      }
      return range;
    },
    computePageRange() {
      this.pages = [];
      var fromPage = this.startFromPage();
      var toPage = this.upToPage();

      this.pages.push(this.firstPage);

      // set starting dots
      if (this.currentPage - this.firstPage > 4) {
        this.pages.push("...");
      }

      for (let pageCount = fromPage; pageCount <= toPage; pageCount++) {
        this.pages.push(pageCount);
      }

      // set ending dots
      if (this.lastPage - this.currentPage > 3) {
        this.pages.push("...");
      }

      this.pages.push(this.lastPage);
    },
  },

  computed: {
    shouldPaginate() {
      if (this.nextPageUrl || this.previousPageUrl) {
        this.$emit("isPaginated");
        return true;
      }
      return false;
    },
  },
  watch: {
    dataset() {
      this.initialize();
      this.computePageRange();
    },
  },
  created() {
    this.initialize();
    this.computePageRange();
  },
};
</script>

<style lang="scss" scoped>
</style>