<template>
  <div dusk="names-autocomplete-component">
    <input
      @blur="hideTypeMore"
      @focus="showTypeMore"
      ref="searchInput"
      class="bg-white form-input"
      :class="styleClasses"
      :id="inputName"
      v-model="query"
      @input="handleOnInput"
      :name="inputName"
      :placeholder="inputPlaceholder"
      autocomplete="off"
    />
    <div
      v-show="typeMore"
      class="absolute text-black-semi text-xs bg-white p-2 shadow-xl rounded"
    >
      Please enter {{ charactersLeft }} or more characters.
    </div>
    <ul class="absolute shadow-xl w-48" v-show="currentlySearching">
      <ul class="bg-opacity-1 bg-white rounded scrolling-auto">
        <li
          v-for="suggestion in suggestions"
          @click="appendToInput(suggestion)"
          class="hover:bg-blue-lighter p-2 pr-4 text-sm cursor-pointer"
        >
          <span v-html="highlight(suggestion)"> </span>
        </li>
      </ul>
    </ul>
  </div>
</template>
p
<script>
export default {
  name: "NamesAutocompleteInput",
  props: {
    firstName: {
      type: String,
      default: "",
      required: false,
    },
    inputPlaceholder: {
      type: String,
      default: "",
      required: false,
    },
    styleClasses: {
      type: String,
      required: false,
      default: "",
    },
    inputName: {
      type: String,
      default: "",
    },
  },
  computed: {
    path() {
      return "/ajax/search/names/";
    },
  },
  data() {
    return {
      charactersLeft: 2,
      minimumCharacters: 2,
      typeMore: false,
      currentlySearching: false,
      query: this.firstName,
      suggestions: [],
    };
  },
  methods: {
    hideTypeMore() {
      this.typeMore = false;
    },
    showTypeMore() {
      this.typeMore = true;
    },
    handleOnInput() {
      this.broadcastInput();
      this.search();
    },
    search() {
      var currentSearchTerm = this.getLastName(this.query);
      if (this.queryIsShort(currentSearchTerm)) {
        this.showTypeMore();
        this.calculateCharactersLeft(currentSearchTerm);
        this.notSearching();
      } else {
        this.hideTypeMore();
        this.searching();
        this.getSuggestions(currentSearchTerm);
      }
    },
    getSuggestions(currentSearchTerm) {
      axios
        .get(this.path + currentSearchTerm)
        .then(({ data }) => (this.suggestions = data))
        .catch((error) => console.log(error.response.data));
    },
    getLastName(allNames) {
      if (!allNames.includes(",")) {
        return allNames;
      } else {
        var namesArray = allNames.split(",");
        var lastName = namesArray[namesArray.length - 1].trim();
        return lastName;
      }
    },
    appendToInput(name) {
      var allNames = this.query.split(",");
      allNames[allNames.length - 1] = name;
      this.query = allNames.join(", ") + ", ";
      this.focusInput();
      this.notSearching();
      this.charactersLeft = this.minimumCharacters;
    },
    focusInput() {
      this.$refs["searchInput"].focus();
    },
    searching() {
      this.currentlySearching = true;
    },
    notSearching() {
      this.currentlySearching = false;
    },
    queryIsShort(currentSearchTerm) {
      return currentSearchTerm.length <= 1;
    },
    calculateCharactersLeft(currentSearchTerm) {
      this.charactersLeft = this.minimumCharacters - currentSearchTerm.length;
    },
    highlight(suggestion) {
      var lastName = this.getLastName(this.query);
      return "<b>" + lastName + "</b>" + suggestion.slice(lastName.length);
    },
    broadcastInput() {
      this.$emit("input", this.query);
    },
  },
};
</script>

<style lang="scss" scoped>
</style>