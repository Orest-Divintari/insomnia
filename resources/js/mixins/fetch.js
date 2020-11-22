export default {
    methods: {
        fetchData() {
            axios
              .get(this.path)
              .then(({ data }) => this.refresh(data))
              .catch((error) => console.log(error));
          },
          fetchMore() {
            axios
              .get(this.dataset.next_page_url)
              .then(({ data }) => this.refresh(data))
              .catch((error) => console.log(error));
           }
    }
};
