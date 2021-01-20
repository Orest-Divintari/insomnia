<template>
  <div>
    <ais-instant-search :search-client="searchClient" index-name="users">
      <ais-configure :hitsPerPage="resultsLimit" />
      <ais-autocomplete ref="autocomplete">
        <div slot-scope="{ currentRefinement, indices, refine }">
          <input
            @blur="typeMore = false"
            @focus="typeMore = true"
            class="bg-white form-input"
            :class="styleClasses"
            autocomplete="off"
            ref="searchInput"
            type="search"
            :value="searchQuery"
            @input="
              searchQuery = $event.currentTarget.value;
              search(refine, $event.currentTarget.value);
            "
            :name="name"
            :placeholder="inputPlaceholder"
          />

          <div
            v-if="typeMore"
            class="absolute text-black-semi text-xs bg-white p-2 shadow-xl rounded"
          >
            Please enter {{ charactersLeft }} or more characters.
          </div>
          <ul
            class="absolute shadow-xl"
            v-show="currentlySearching"
            v-for="index in indices"
            :key="index.indexId"
          >
            <li>
              <ul class="bg-opacity-1 bg-white rounded scrolling-auto">
                <li
                  v-for="hit in index.hits"
                  @click="addToInput(hit)"
                  :key="hit.objectID"
                  class="hover:bg-blue-lighter p-2 pr-4 text-sm cursor-pointer"
                >
                  <ais-highlight
                    attribute="name"
                    :hit="hit"
                    :class-names="{
                      'ais-Highlight-highlighted':
                        'font-semibold bg-transparent',
                    }"
                  />
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </ais-autocomplete>
    </ais-instant-search>
  </div>
</template>
p
<script>
import algoliasearch from "algoliasearch/lite";
import { VueAutosuggest } from "vue-autosuggest";

export default {
  components: {
    VueAutosuggest,
  },
  props: {
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
    name: {
      type: String,
      default: "",
    },
    resultsLimit: {
      type: Number,
      required: false,
      default: 10,
    },
  },
  watch: {
    searchQuery(newValue, oldValue) {
      this.$emit("input", newValue);
    },
  },
  data() {
    return {
      charactersLeft: 2,
      minimumCharacters: 2,
      typeMore: false,
      currentlySearching: false,
      searchQuery: "",
      searchClient: algoliasearch(
        "1LXOIX8OXI",
        "98af466cb49a56ef06819bf9b1736ed8"
      ),
      commaSeparatedValues: "",
      currentSearchTerm: "",
      allSearchTerms: [],
    };
  },
  methods: {
    search(refine, currentInput) {
      var currentSearchTerm = this.lastValue(currentInput);
      var termLength = currentSearchTerm.length;
      if (termLength > 1) {
        refine(currentSearchTerm);
        this.typeMore = false;
        this.currentlySearching = true;
      }
      if (termLength <= 1) {
        this.typeMore = true;
        this.charactersLeft = this.minimumCharacters - termLength;
        this.currentlySearching = false;
      }
    },
    currentSearchRefinement(refinement) {
      this.currentSearchTerm = refinement;
    },
    lastValue(allValues) {
      if (!allValues.includes(",")) {
        return allValues;
      } else {
        var allSearchTerms = allValues.split(",");
        var lastTerm = allSearchTerms[allSearchTerms.length - 1].trim();
        return lastTerm;
      }
    },
    onSelect(selected) {
      if (selected) {
        this.query = selected.item.name;
      }
    },
    indicesToSuggestions(indices) {
      return indices.map(({ hits }) => ({ data: hits }));
    },
    addToInput(value) {
      var allValues = this.searchQuery.split(",");
      allValues[allValues.length - 1] = value.name;
      this.searchQuery = allValues.join(", ") + ", ";
      this.$refs["searchInput"].focus();
      this.currentlySearching = false;
      this.charactersLeft = this.minimumCharacters;
      // this.$refs["autocomplete"].state.indices = [];
    },
  },
};
</script>

<style lang="scss" scoped>
</style>