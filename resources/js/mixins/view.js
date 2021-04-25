import EventBus from "../eventBus";

export default {
    methods: {
        showProfile(user) {
            window.location.href = "/profiles/" + user.name;
        },
        showPost(post) {
            window.location.href =
                "/profile-posts/" + post.id;
        },
        showComment(comment){
            window.location.href="/profile-posts/comments/" + comment.id;
        },
        showReply(reply) {
            window.location.href = "/replies/" + reply.id;
        },
        showCategory(category) {
            window.location.href = "/forum/categories/" + category.slug;
        },
        showConversation(conversation){
            window.location.href="/conversations/" + conversation.slug;
        },
        showThread(thread) {
            window.location.href = "/threads/" + thread.slug;
        },
        showMessage(message){
            window.location.href="/messages/" + message.id;
        }
    }
};
