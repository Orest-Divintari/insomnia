/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./bootstrap");

window.Vue = require("vue");

Vue.directive("click-outside", {
    bind: function(el, binding, vnode) {
        el.clickOutsideEvent = function(event) {
            // here I check that click was outside the el and his childrens
            if (el !== event.target && !el.contains(event.target)) {
                // and if it did, call method provided in attribute value
                vnode.context[binding.expression](el);
                event.stopPropagation();
            }
        };
        document.addEventListener("click", el.clickOutsideEvent);
    },
    unbind: function(el) {
        document.removeEventListener("click", el.clickOutsideEvent);
    }
});

Vue.component(
    "example-component",
    require("./components/ExampleComponent.vue").default
);
Vue.component("hamburger", require("./components/Hamburger.vue").default);
Vue.component("Threads", require("./components/Threads.vue").default);
Vue.component("Thread", require("./components/Thread.vue").default);
Vue.component("Replies", require("./components/Replies.vue").default);
Vue.component("search", require("./components/Search.vue").default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: "#app"
});
