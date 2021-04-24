export default {
    methods: {
        fetchData() {
            axios
              .get(this.path)
              .then(({ data }) => this.refresh(data))
              .catch((error) => console.log(error));
          },
          fetchMore() {
            let nextPage = parseInt(this.dataset.current_page)  + 1;
            axios
              .get(this.path + '?page=' + nextPage)
              .then(({ data }) => this.refresh(data))
              .catch((error) => console.log(error));
           }
    }
};
