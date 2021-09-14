import store from "../js/store";
import "./plugins";
import "./globals";
import "./directives";
import "./helpers";
import "./components";
import "./components/base";

// update visitor on page load
if(window.App.signedIn){
    store.updateVisitor(window.App.visitor);
}