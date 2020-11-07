export default {
    methods: {
        showReply(reply) {
            window.location.href = "/api/replies/" + reply.id;
        },
        updated() {
            this.editing = false;
        },
        updateLikeStatus(status) {
            this.isLiked = status;
            status ? this.likesCount++ : this.likesCount--;
        },
        fetchMore() {
            axios
                .get(this.dataset.next_page_url)
                .then(({ data }) => this.refresh(data))
                .catch(error => console.log(error));
        }
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
