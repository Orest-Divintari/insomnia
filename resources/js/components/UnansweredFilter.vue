<template>
  <div class="dropdown-item">
    <label class="form-label" for="unanswered">Unanswered</label>
    <input
      :checked="notAnswered"
      ref="unasnwered"
      class="from-input"
      type="checkbox"
      id="unanswered"
    />
  </div>
</template>

<script>
export default {
  computed: {
    notAnswered() {
      return window.location.href.includes("?unanswered=1");
    }
  }
};
</script>

<style lang="scss" scoped>
</style>