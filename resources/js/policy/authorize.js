let authorize = {
    owns(user, model) {
        return user.id == model.user_id || user.id == model.profile_user_id;
    },
    is(user, profileUser) {
        return user.id == profileUser.id;
    }
};
export default authorize;
