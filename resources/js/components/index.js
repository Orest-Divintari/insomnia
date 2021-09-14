import Vue from "vue";
import Threads from "./threads/Threads";
import Thread from "./threads/Thread";
import Search from "./search/Search";
import NotificationsButton from "./notifications/NotificationsButton";
import Profile from "./profile/Profile";
import ForumSearch from "./search/ForumSearch";
import SearchResults from "./search/SearchResults";
import Conversations from "./conversations/Conversations";
import Conversation from "./conversations/Conversation";
import ConversationsButton from "./conversations/ConversationsButton";
import ProfileButton from "./profile/ProfileButton";
import EditEmailModal from "./account/EditEmailModal";
import FollowButton from "./follows/FollowButton";
import PasswordInput from "./account/PasswordInput";
import AccountNotifications from "./account/AccountNotifications";
import BirthDateCheckbox from "./account/BirthDateCheckbox";
import WatchOnCreationCheckbox from "./account/WatchOnCreationCheckbox";
import WatchOnInteractionCheckbox from "./account/WatchOnInteractionCheckbox";
import IgnoreUserButton from "./ignore/IgnoreUserButton";
import IgnoreThreadButton from "./ignore/IgnoreThreadButton";
import ThreadListItem from "./threads/ThreadListItem";
import ProfilePosts from "./profilePosts/ProfilePosts";

[
    Threads,
    Thread,
    Search,
    NotificationsButton,
    Profile,
    ForumSearch,
    SearchResults,
    Conversations,
    Conversation,
    ConversationsButton,
    ProfileButton,
    EditEmailModal,
    FollowButton,
    PasswordInput,
    AccountNotifications,
    BirthDateCheckbox,
    WatchOnCreationCheckbox,
    WatchOnInteractionCheckbox,
    IgnoreUserButton,
    IgnoreThreadButton,
    ThreadListItem,
    ProfilePosts,
    ,
].forEach(Component => {
    Vue.component(Component.name, Component);
})

