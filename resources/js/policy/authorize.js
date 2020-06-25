let authorize = {
    owns(user, model) {
        return user.id == model.user_id;
    }
};
export default authorize;
