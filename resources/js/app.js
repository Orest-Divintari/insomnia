/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./bootstrap");

window.Vue = require("vue");

Vue.component("hamburger", require("./components/Hamburger.vue").default);
Vue.component("Threads", require("./components/threads/Threads.vue").default);
Vue.component("Thread", require("./components/threads/Thread.vue").default);
Vue.component("Replies", require("./components/threads/Replies.vue").default);
Vue.component("search", require("./components/Search.vue").default);
Vue.component("Wysiwyg", require("./components/Wysiwyg.vue").default);
Vue.component(
    "Notification",
    require("./components/notifications/Notification.vue").default
);
Vue.component("Dropdown", require("./components/Dropdown.vue").default);
Vue.component("Profile", require("./components/profile/Profile.vue").default);
Vue.component(
    "NewComment",
    require("./components/profile/NewComment.vue").default
);
Vue.component("Comment", require("./components/profile/Comment.vue").default);
Vue.component(
    "SearchBar",
    require("./components/search/SearchBar.vue").default
);
Vue.component(
    "SearchResults",
    require("./components/search/SearchResults.vue").default
);
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: "#app"
});
