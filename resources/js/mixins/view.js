import EventBus from "../eventBus";

export default {
    methods: {
        showProfile(user) {
            window.location.href = "/profiles/" + user.name;
        },
        showPost(user, post) {
            EventBus.$emit("selectTab", "Profile Posts");
            window.location.href =
                "/profiles/" + user.name + "#profile-post-" + post.id;
        },
        showReply(reply) {
            window.location.href = "/api/replies/" + reply.id;
        },
        showCategory(category) {
            window.location.href = "/forum/categories/" + category.slug;
        },
        showThread(thread) {
            window.location.href = "/threads/" + thread.slug;
        }
    }
};
