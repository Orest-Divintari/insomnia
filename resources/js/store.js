// the methods and variables are named from the point of view of the authenticated user
// for example, the authenticated isFollowing a given user
export const store = {
    state: {
        profiles: {},
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