import Vue from "vue";

window.showErrorModal = function(message) {
    EventBus.$emit("error", message);
};

