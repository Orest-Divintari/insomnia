import Vue from "vue";
import authorization from "./policy/authorize";
import EventBus from "./eventBus";
window.Vue = Vue;
window._ = require("lodash");

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

// try {
//     window.Popper = require("popper.js").default;
//     window.$ = window.jQuery = require("jquery");

//     require("bootstrap");
// } catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require("axios");

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

Vue.prototype.user = window.App.user;
// -------  authentication ---------
Vue.prototype.signedIn = window.App.signedIn;

// ----------- authorization ----------
Vue.prototype.authorize = function(policy, model) {
    let user = window.App.user;
    if (!window.App.signedIn) return false;

    if (typeof policy == "string") {
        return authorization[policy](user, model);
    }
};

// ----------- custom directives ----------
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

Vue.directive("focus", {
    // When the bound element is inserted into the DOM...
    inserted: function(el) {
        // Focus the element
        el.focus();
    }
});


// -------- global error modal message function --------
window.showModalError = function(message) {
    EventBus.$emit("error", message);
};


/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });

import VTooltip from "v-tooltip";

Vue.use(VTooltip);

import VModal from "vue-js-modal";

Vue.use(VModal);
