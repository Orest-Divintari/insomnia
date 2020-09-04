export default {
    methods: {
        ownsProfile() {
            return this.authorize("is", this.notificationData.profileOwner);
        },
        ownsPost() {
            return this.authorize("owns", this.notificationData.profilePost);
        },
        ownsComment() {
            return this.authorize("owns", this.notificationData.comment);
        }
    }
};
