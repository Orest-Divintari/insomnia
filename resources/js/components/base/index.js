import Vue from "vue";
import Hamburger from "./Hamburger";
import Wysiwyg from "./Wysiwyg";
import Dropdown from "./Dropdown";
import ErrorModal from "./ErrorModal";
import NamesAutocompleteInput from "./NamesAutocompleteInput";
import ProfilePopover from "./ProfilePopover";
import Paginator from "./Paginator";
import Avatar from "./Avatar";
import HtmlToText from "./HtmlToText";


[
    Hamburger,
    Wysiwyg,
    Dropdown,
    ErrorModal,
    NamesAutocompleteInput,
    ProfilePopover,
    Paginator,
    Avatar,
    HtmlToText,
].forEach(Component => {
    Vue.component(Component.name, Component);
})

