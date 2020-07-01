export default {
    methods: {
        showReply(reply) {
            window.location.href = "/api/replies/" + reply.id;
        }
    }
};
