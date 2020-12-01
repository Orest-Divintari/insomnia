<template>
  <div>
    <dropdown :styleClasses="'w-80'">
      <template v-slot:dropdown-trigger>
        <a
          class="fas fa-search text-blue-dark cursor-pointer hover:text-blue-mid"
        ></a>
      </template>
      <template v-slot:dropdown-items>
        <p class="dropdown-title text-sm font-thin">Search</p>
        <form ref="form" action="/search" method="GET" class="mb-0">
          <div class="bg-blue-lighter py-3">
            <div class="px-2">
              <input
                v-focus
                class="w-full text-smaller text-black-semi p-1 rounded focus:outline-none"
                type="text"
                ref="q"
                name="q"
                placeholder="Search..."
              />
              <div class="flex flex-row-reverse mt-4 justify-end pl-1">
                <label class="text-xs text-black-semi ml-2" for="only_title"
                  >Search titles only</label
                >
                <input
                  @click="requestTitle"
                  type="checkbox"
                  ref="onlyTitle"
                  id="only_title"
                  name="onlyTitle"
                />
              </div>
              <div class="flex text-black-semi items-center mt-3 mb-2">
                <label class="text-xs mr-3" for="by_member">By:</label>
                <input
                  type="text"
                  class="text-smaller rounded p-1 w-full focus:outline-none"
                  id="by_member"
                  name="postedBy"
                  ref="postedBy"
                  placeholder="Member"
                />
              </div>
            </div>
            <hr />
            <div class="flex justify-end mt-2 pr-2 text-xs items-center">
              <button @click.prevent="search" class="form-button-small mr-2">
                <span class="fas fa-search text-2xs"></span> Search
              </button>
              <a
                href="/search/advanced"
                class="hover:bg-blue-light p-2 rounded text-blue-mid"
                >Advanced search...</a
              >
            </div>
          </div>
        </form>
      </template>
    </dropdown>
  </div>
</template>

<script>
export default {
  methods: {
    requestTitle() {
      var onlyTitle = this.$refs.onlyTitle;
      onlyTitle.value = onlyTitle.checked;
    },
    search() {
      for (var ref in this.$refs) {
        var element = this.$refs[ref];
        if (element.value == "") {
          element.disabled = true;
        }
      }
      this.$refs["form"].submit();
    },
  },
};
</script>

<style lang="scss" scoped>
</style>