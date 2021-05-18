

let authorize = {
    owns(authUser, model) {
        return authUser.id == model.user_id || authUser.id == model.user_id;
    },
    is(authUser, user) {
        return authUser.id == user.id;
    },
    postOnProfile(model){
        return model.privacy['post_on_profile'];
    },
    startConversation(model){
        return model.privacy('start_conversation')
    }
    
};
export default authorize;
