export default{
    data() {
        return {
        isLiked: this.item.is_liked ?? false ,
        likesCount: this.item.likes_count ?? 0,
        }
    },
    methods:{
        updateLikeStatus(liked){
            this.isLiked = liked;
            liked ? this.likesCount++ : this.likesCount--;
        },
    },
    computed: {
        hasLikes(){
            return this.likesCount > 0;
        },
    }
}