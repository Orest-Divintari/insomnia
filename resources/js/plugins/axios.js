var axios = require("axios");
import store from "../store";

axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";


axios.interceptors.response.use(
    response => {
        if (!window.App.signedIn){
            return Promise.resolve(response);
        }
        // response.data.data contains the data that are returned from the controllers
        // response.data.visitor contains the data that are appended by the middleware
        // then we reassign the response.data.data to response.data which is the structure that vue components expect
        store.updateVisitor(response.data.visitor);
        delete response.data.visitor;
        response.data = response.data.data;
        return Promise.resolve(response);
}, error => {
    if (!window.App.signedIn){
        return Promise.reject(error);
    }
    // error.response.data.data contains the error message that is returned by laravel
    // error.response.data.visitor contains the data that are appended by the middleware
    // then we reassign the error.response.data.data to error.response.data 
    // because vue components expects the error message to be in error.response.data
    store.updateVisitor(error.response.data.visitor);
    delete error.response.data.visitor;
    error.response.data = error.response.data.data;
    return Promise.reject(error);
});

export default axios;