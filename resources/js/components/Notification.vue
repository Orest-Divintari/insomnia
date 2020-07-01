<template>
  <div>
    <dropdown>
      <template v-slot:dropdown-trigger>
        <i @click="fetchData" class="fas fa-bell"></i>
      </template>
      <template v-slot:dropdown-items>
        <div class="dropdown-title">Alerts</div>
        <div v-for="(item, index) in items" :key="item.id" class="dropdown-item">{{ item.id }}</div>
      </template>
    </dropdown>
  </div>
</template>

<script>
export default {
  data() {
    return {
      items: []
    };
  },
  computed: {
    path() {
      return "/api/notifications";
    }
  },
  methods: {
    refresh({ data }) {
      this.items = data;
    },
    fetchData() {
      axios
        .get(this.path)
        .then(response => console.log(response))
        .catch(error => console.log(error));
    }
  }
};
</script>

<style lang="scss" scoped>
</style>