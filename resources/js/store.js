// the methods and variables are named from the point of view of the authenticated user
// the authenticated user has a followsList
// the authenticated user can follow/unfollow a given user
export const store = {
    state: {
        followsList: {}
    },
    // determine whether the authenticated user is following the given user
    isFollowing(user){
            return this.state.followsList[user.id];
    },
    updateFollow(user, isFollowing){
        this.state.followsList[user.id] = isFollowing;
    },
    // determine if the user exists in the followsList
    followExists(user){
        return user.id in this.state.followsList;
    }
  };