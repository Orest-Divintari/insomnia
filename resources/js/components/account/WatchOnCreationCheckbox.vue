<template>
  <div>
    <div class="flex flex-row-reverse items-center justify-end mt-5/2">
      <label for="subscribe-on-creation" class="text-black-semi text-sm"
        >Automatically subscribe to content you create…</label
      >
      <input
        class="mt-1/2 mr-2"
        type="checkbox"
        id="subscribe-on-creation"
        name="subscribe_on_creation"
        v-model="subscribeWithoutEmail"
      />
    </div>

    <div class="flex flex-row-reverse items-center justify-end mt-2 ml-5">
      <label for="subscribe-with-email" class="text-black-semi text-sm"
        >and receive email notifications</label
      >
      <input
        class="mt-1/2 mr-2"
        type="checkbox"
        name="subscribe_on_creation_with_email"
        id="subscribe-with-email"
        v-model="subscribeWithEmail"
        :disabled="!subscribeWithoutEmail"
      />
    </div>
  </div>
</template>

<script>
export default {
  props: {
    user: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      subscribeWithoutEmail: this.user.preferences["subscribe_on_creation"],
      subscribeWithEmail: this.user.preferences[
        "subscribe_on_creation_with_email"
      ],
    };
  },
};
</script>

<style lang="scss" scoped>
</style>