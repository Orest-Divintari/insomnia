<x-layouts.forum>
    @push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.2.3/trix.css">
    @endpush
    <thread :thread="{{ $thread }}" inline-template v-cloak>
        <div>
            <header>
                <h1 class="font-bold text-3xl mb-1" v-text="title"></h1>
                <div class="flex items-center text-smaller text-gray-lightest">
                    <span class="fas fa-user mr-1"></span>
                    <a href="" class="hover:underline">
                        {{ $thread->poster->shortName }}</a>
                    <p class=" dot"></p>
                    <span class="mr-1 fas fa-clock"></span>
                    <a href="{{route('threads.show', $thread->slug)}}" class="hover:underline">
                        {{ $thread->date_created }}</a>
                    <p class=" dot"></p>
                    <i class="fas fa-tags text-xs "></i>
                    <div class="flex">
                        @foreach($thread->tags as $tag)
                        <a href="{{ route('tags.show', $tag->name) }}" class="tag-link">
                            {{ $tag->name }}
                        </a>
                        @endforeach
                    </div>
                    <p class="dot"></p>
                    <i class="fas fa-long-arrow-alt-down mr-1 "></i>
                    <a v-if="sortedByLikes" href="{{ route('threads.show', $thread )}}" class="hover:underline">Sort
                        (Post Date) </a>
                    <a v-else href="{{ route('threads.show', $thread ) . '?sort_by_likes=1'}}"
                        class="hover:underline">Sort
                        (Likes) </a>


                </div>
            </header>

            <main class="section">
                <x-breadcrumb.container>
                    <x-breadcrumb.item :title="'Forum'" :route="route('forum')"></x-breadcrumb.item>
                    @if(!$thread->category->isRoot() && !$thread->category->hasSubCategories())
                    <x-breadcrumb.item :title="$thread->category->group->title" :route="route('forum')">
                    </x-breadcrumb.item>
                    <x-breadcrumb.item :title="$thread->category->category->title"
                        :route="route('categories.show', $thread->category->category->slug)">
                    </x-breadcrumb.item>
                    @endif
                    <x-breadcrumb.leaf :title="$thread->category->title"
                        :route="route('categories.show', $thread->category->slug)">
                    </x-breadcrumb.leaf>
                </x-breadcrumb.container>

                <div class="mt-7 flex justify-end">

                    @auth
                    <subscribe-button thread-slug="{{ $thread->slug }}"
                        subscription-status="{{ json_encode($thread->subscribedByAuthUser)}}">
                    </subscribe-button>
                    @endauth

                    @if(auth()->user()->can('ignore', $thread) || auth()->user()->can('unignore', $thread))
                    <ignore-thread-button class="mr-1" :thread="{{ $thread }}"
                        :ignored="{{ json_encode($thread->ignored_by_visitor) }}">
                    </ignore-thread-button>
                    @endif

                    @if(auth()->check() && Gate::allows('lock', $thread))
                    <lock-thread-button :thread="{{ $thread }}"></lock-thread-button>
                    @endif

                    @if(auth()->check() && auth()->user()->isAdmin())
                    <button @click="togglePin" v-if="pinned" class="btn-white-blue mr-1">Unpin</button>
                    <button @click="togglePin" v-else class="btn-white-blue mr-1">Pin</button>
                    @endif
                    @if(Gate::allows('manage', $thread))
                    <dropdown>
                        <template v-slot:dropdown-trigger>
                            <div class="btn-white-blue flex items-center "> <span
                                    class="text-xl fas fa-ellipsis-h leading-none mr-3/2"></span>
                                <span class="fas fa-sort-down text-2xs leading-none pb-1"></span>
                            </div>
                        </template>
                        <template v-slot:dropdown-items>
                            <div class="dropdown-title">More
                                Options
                            </div>
                            <div @click="edit" class="dropdown-item">
                                Edit
                                Thread
                            </div>
                        </template>
                    </dropdown>

                    <modal name="edit-thread" height='auto'>
                        <div class="form-container">
                            <div
                                class="flex justify-between items-center bg-blue-light text-lg text-black-semi border-b border-blue-light py-3 px-3">
                                <p> Edit Thread </p>
                                <button @click="hideEditModal" class="fas fa-times"></button>
                            </div>
                            <form>

                                <!-- ROW -->
                                <div class="form-row">
                                    <!-- LEFT -->
                                    <div class="form-left-col ">
                                        <label class="form-label" for="title">Title:</label>
                                    </div>
                                    <!-- RIGHT -->
                                    <div class="form-right-col">
                                        <p class="form-label-phone">Title:</p>
                                        <div>
                                            <input v-model="title" class="form-input" type="text" id="title"
                                                name="title" required autocomplete="title">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-button-container justify-center">
                                    <button @click="update" type="button" class="form-button ">Save</button>
                                </div>

                            </form>
                        </div>
                    </modal>
                    @endif
                </div>



                <replies :repliable="thread" :replies="{{ json_encode($replies) }}"></replies>
            </main>
        </div>
    </thread>

</x-layouts.forum>