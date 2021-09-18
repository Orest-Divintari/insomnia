<template>
  <div dusk="names-autocomplete-component">
    <input
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
      v-show="typeMore && inputIsEmpty"
      class="absolute text-black-semi text-xs bg-white p-2 shadow-xl rounded"
    >
      Please enter {{ charactersLeft }} or more characters.
    </div>
    <div v-if="hasNotExceededLimit">
      <ul class="absolute shadow-xl w-48" v-show="currentlySearching">
        <ul class="bg-opacity-1 bg-white rounded scrolling-auto">
          <li
            v-for="(suggestion, index) in suggestions"
            :key="index"
            @click="appendToInput(suggestion)"
            class="hover:bg-blue-lighter p-2 pr-4 text-sm cursor-pointer"
          >
            <span v-html="highlight(suggestion)"> </span>
          </li>
        </ul>
      </ul>
    </div>
  </div>
</template>
p
<script>
export default {
  name: "NamesAutocompleteInput",
  props: {
    suggestionsNumber: {
      type: Number,
      default: 99,
      required: false,
    },
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
    inputIsEmpty() {
      return this.query.length == 0;
    },
    namesArray() {
      return this.query.split(",");
    },
    namesCount() {
      return this.query.split(",").length;
    },
    hasNotExceededLimit() {
      return this.suggestionsNumber >= this.namesCount;
    },
    canEnterMoreNames() {
      return this.suggestionsNumber > this.namesCount;
    },
    path() {
      return "/ajax/search/names/";
    },
  },
  data() {
    return {
      suggestionLimit: false,
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
      var allNames = this.namesArray;
      allNames[allNames.length - 1] = name;
      this.formatInput(allNames);
      this.focusInput();
      this.notSearching();
      this.broadcastInput();
      this.charactersLeft = this.minimumCharacters;
    },
    formatInput(allNames) {
      this.query = allNames.join(", ");
      if (this.canEnterMoreNames) {
        this.query = this.query + ", ";
      }
    },
    exceededSuggestionLimit() {
      return this.namesCount >= this.suggestionsNumber;
    },
    broadcastInput() {
      this.$emit("input", this.query);
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