<template>
  <div
    class="text-blue-mid hover:text-blue-mid-light text-xs"
    @click.stop="toggleRead"
  >
    <div v-if="read && hovering">
      <v-popover
        placement="top"
        offset="10"
        trigger="hover"
        popoverWrapperClass="border-black"
        popoverInnerClass="bg-black py-1 px-2 text-white text-xs rounded"
        :popperOptions="{
          modifiers: { preventOverflow: { escapeWithReference: true } },
        }"
      >
        <button class="focus:outline-none">
          <span class="inline p-2">
            <i class="far fa-circle"></i>
          </span>
        </button>
        <template slot="popover"> Mark unread </template>
      </v-popover>
    </div>
    <div v-if="!read">
      <v-popover
        placement="top"
        offset="10"
        trigger="hover"
        popoverWrapperClass="border-black"
        popoverInnerClass="bg-black py-1 px-2 text-white text-xs rounded"
        :popperOptions="{
          modifiers: { preventOverflow: { escapeWithReference: true } },
        }"
      >
        <button class="focus:outline-none">
          <span class="inline p-2">
            <i class="fas fa-circle"></i>
          </span>
        </button>
        <template slot="popover"> Mark read </template>
      </v-popover>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    hovering: {
      type: Boolean,
      required: true,
    },
    notification: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      read: this.notification.is_read,
    };
  },
  methods: {
    toggleRead() {
      this.read = !this.read;
      this.$emit("toggleRead");
    },
  },
};
</script>

<style lang="scss" scoped>
</style>