import Vue from "vue"


Vue.directive("focus", {
    // When the bound element is inserted into the DOM...
    inserted: function(el) {
        // Focus the element
            el.focus();
    },
    //  When the containing component’s VNode is updated
    update: function(el,binding){
        // focus the element when the binding value is true
        if(binding.value){
            el.focus();
        }
         
        
    }
});