// the methods and variables are named from the point of view of the authenticated user


// for example, the authenticated user isFollowing a given user
export default {
    state: {
        profiles: {},
        visitor: {}
    },
    updateVisitor(visitor){
        this.state.visitor['avatar_path'] = visitor.avatar_path;
        this.state.visitor['unread_conversations_count'] = visitor.unread_conversations_count ;
        this.state.visitor['unviewed_notifications_count'] = visitor.unviewed_notifications_count;
        this.state.visitor['default_avatar'] = visitor.default_avatar;
    },
    getVisitor(){
        return this.state.visitor;
    },
    addOrUpdateProfile(user){
        this.state.profiles[user.id] = user;
    },
    updateFollow(user, isFollowing){
        this.state.profiles[user.id].followed_by_visitor = isFollowing;
    },
    // determine if the user exists in the profiles object
    profileExists(user){
        return user.id in this.state.profiles;
    },
    getProfile(user){
        return this.state.profiles[user.id];
    }
  };
