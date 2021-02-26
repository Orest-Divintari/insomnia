export default {
      methods: {
        toggle(isChecked, filterType) {
          if (this.filters[filterType] == true) {
            this.removeFilter(filterType);
          } else {
            this.filters[filterType] = isChecked;
          }
        },
        removeFilter(filterType) {
          Vue.delete(this.filters, filterType);
          delete this.form[filterType];
          this.apply();
        },
        apply() {
          this.updateFilters();
          var path = window.location.pathname + "?";
          for (const [key, value] of Object.entries(this.filters)) {
            path = path + this.toSnakeCase(key) + "=" + value + "&";
          }
          window.location.href = path;
        },
        toSnakeCase(input){
          return input.replace( /([A-Z])/g, "_$1").toLowerCase();
        },
        updateFilters() {
          for (var filter in this.form) {
            if (this.form[filter] != "") {
              this.filters[filter] = this.form[filter];
            }
          }
        },
      },
      created() {
        for (var filter in this.filters) {
          this.form[filter] = this.filters[filter];
        }
      },
}