import Vue from "vue";
import EventBus from "../eventBus";

window.showErrorModal = function(message) {
    EventBus.$emit("error", message);
};

