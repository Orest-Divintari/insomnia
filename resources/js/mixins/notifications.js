export default {
    methods: {
        showProfile(user) {
            window.location.href = "/profiles/" + user.name;
        },
        showPost() {
            window.location.href =
                "/profiles/" +
                this.notificationData.profileOwner.name +
                "#profile-post-" +
                this.notificationData.profilePost.id;
        },
    }
};
