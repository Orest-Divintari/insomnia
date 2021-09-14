let authorize = {
    owns(authUser, model) {
        return authUser.id == model.user_id;
    },
    is(authUser, user) {
        return authUser.id == user.id;
    },
};

export default authorize;
