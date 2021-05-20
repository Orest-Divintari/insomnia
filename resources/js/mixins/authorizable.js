export default {
    methods: {
        can(ability, model){
            return model.permissions[ability];
        },
        isAuthUser(user){
            return this.authorize('is', user);
        },
        ownsProfile(profileOwner) {
            return this.authorize("is", profileOwner);
        },
        ownsPost(post) {
            return this.authorize("owns", post);
        },
        ownsComment(comment) {
            return this.authorize("owns", comment);
        },
    }
}