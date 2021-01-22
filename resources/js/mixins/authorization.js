export default {
    methods: {
        ownsProfile(profileOwner) {
            return this.authorize("is", profileOwner);
        },
        ownsPost(post) {
            return this.authorize("owns", post);
        },
        ownsComment(comment) {
            return this.authorize("owns", comment);
        },
        isAuthUser(user){
            return this.authorize('is', user);
        }
        
    }
};
