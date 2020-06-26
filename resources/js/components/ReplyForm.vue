<template>
  <div class="mt-6">
    <div class="reply-container">
      <div class="reply-left-col">
        <img :src="user.avatar_path" class="avatar-large" alt />
      </div>
      <div class="w-full p-3">
        <wysiwyg
          v-model="form.body"
          
          name="body"
          classes="h-32 text-sm"
          placeholder="Write your reply..."
        ></wysiwyg>
        <button @click="post" class="mt-4 form-button">Post Reply</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      form: {
        body: ""
      }
    };
  },
  methods: {
      path(){
          return window.location + '/replies';
      },
      post() {
          axios.post(this.path())
            .then(response => )
      }
  },
};
</script>

<style lang="scss" scoped>
</style>