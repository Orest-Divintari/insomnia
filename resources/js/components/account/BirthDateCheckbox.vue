<template>
  <div>
    <div class="flex flex-row-reverse items-center justify-end mt-4">
      <label for="birth-date" class="text-black-semi text-sm"
        >Show day and month of birth</label
      >
      <input
        class="mt-1/2 mr-2"
        type="checkbox"
        id="birth-date"
        name="show_birth_date"
        v-model="date"
      />
    </div>

    <div class="flex flex-row-reverse items-center justify-end mt-2 ml-5">
      <label for="birth-year" class="text-black-semi text-sm"
        >Show year of birth</label
      >
      <input
        class="mt-1/2 mr-2"
        type="checkbox"
        name="show_birth_year"
        id="birth-year"
        v-model="year"
        :disabled="!date"
      />
    </div>
    <p class="text-gray-shuttle text-smaller ml-10.5">
      This will allow people to see your age.
    </p>
  </div>
</template>

<script>
export default {
  name: "BirthDateCheckbox",
  props: {
    birthDate: {
      type: Boolean,
      required: true,
    },
    birthYear: {
      type: Boolean,
      required: true,
    },
  },
  data() {
    return {
      date: this.birthDate,
      year: this.birthYear,
    };
  },
};
</script>

<style lang="scss" scoped>
</style>