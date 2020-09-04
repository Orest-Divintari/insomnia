let authorize = {
    owns(authUser, model) {
        return authUser.id == model.user_id || authUser.id == model.poster_id;
    },
    is(authUser, user) {
        return authUser.id == user.id;
    }
};
export default authorize;
