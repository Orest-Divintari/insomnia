<template>
  <div>
    <dropdown styleClasses="w-80">
      <template v-slot:dropdown-trigger>
        <div class="relative hover:bg-blue-mid h-14 text-center pt-4 px-2">
          <i class="fas fa-envelope"></i>
        </div>
      </template>
      <template v-slot:dropdown-items>
        <div class="dropdown-title">Conversations</div>

        <div>
          <div class="dropdown-footer-item flex items-center">
            <a href="/conversations" class="blue-link">Show all</a>
            <p class="dot"></p>
            <a href="/conversations/create" class="blue-link"
              >Start a new conversation</a
            >
          </div>
        </div>
      </template>
    </dropdown>
  </div>
</template>

<script>
export default {
  data() {
    return {
      conversations: [],
      conversationsCount: 0,
    };
  },
  computed: {
    conversationsExist() {
      return this.conversationsCount > 0;
    },
    path() {
      return "";
    },
  },
  methods: {
    fetchData() {
      axios
        .get(this.path)
        .then(({ data }) => this.refresh(data))
        .catch((error) => console.log(error));
    },
  },
};
</script>

<style lang="scss" scoped>
</style>