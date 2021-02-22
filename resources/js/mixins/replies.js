export default {
    methods: {
        showReply(reply) {
            window.location.href = "/replies/" + reply.id;
        },
        updated() {
            this.editing = false;
        },
        updateLikeStatus(status) {
            this.isLiked = status;
            status ? this.likesCount++ : this.likesCount--;
        },
    },
    computed: {
        data() {
            return { body: this.body };
        },
        hasLikes() {
            return this.likesCount > 0;
        },
        isThreadReply(){
            return this.reply.repliable_type.includes("Thread");
        }
    }
};
