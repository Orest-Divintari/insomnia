import axios from "./axios";
import Vue from "vue"
import VModal from "vue-js-modal";
import VTooltip from "v-tooltip";

window.axios = axios;
window._ = require('lodash');
Vue.use(VModal);
Vue.use(VTooltip);