import Vue from "vue"
import userPolicy from "../policies/user-policy";

Vue.prototype.authorize = function(policy, model) {
    let user = window.App.user;
    if (!window.App.signedIn) return false;

    if (typeof policy == "string") {
        return userPolicy[policy](user, model);
    }
};
