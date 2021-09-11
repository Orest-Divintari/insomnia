export default {
    methods: {
        highlightQueryWords(words){
            let highlightedWords = words.split(" ").map((word) => {
              if (this.query.includes(word)) {
                return "<strong>" + word + "</strong>";
              }
              return word;
            });
            return highlightedWords.join(" ");
        },
        clean(content){
          return content.replace(/<\/?[^>]+(>|$)/g, "");
        },
        highlight(text) {
          let cleanText = this.clean(text);
          if (this.query != "") {
            return this.highlightQueryWords(cleanText);
          }
          return cleanText;
        },
    }

}