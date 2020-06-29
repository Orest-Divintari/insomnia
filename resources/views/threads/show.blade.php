<x-layouts._forum>
    @push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.2.3/trix.css">
    @endpush
    <thread :thread="{{ $thread }}" inline-template>
        <div>
            <header>
                <h1 class="font-bold text-3xl mb-1" v-text="title"></h1>
                <div class="flex items-center text-smaller text-gray-lightest">
                    <span class="fas fa-user mr-1"></span>
                    <a href="" class="hover:underline">
                        {{ $thread->poster->shortName }}</a>
                    <p class=" dot"></p>
                    <span class="mr-1 fas fa-clock"></span>
                    <a href="{{route('threads.show', $thread->slug)}}" class="mr-1 hover:underline">
                        {{ $thread->date_created }}</a>
                    <p class=" dot"></p>
                    <i class="fas fa-long-arrow-alt-down mr-1 "></i>
                    <p class="hover:underline">Sort</p>
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
                    <button class="btn-thread-control mr-1">Ignore</button>

                    <subscribe-button thread_slug="{{ $thread->slug }}"
                        subscription_status="{{ json_encode($thread->subscribed_by_auth_user)}}" class="mr-1">
                    </subscribe-button>


                    <button class="btn-thread-control mr-1">Lock</button>
                    <button class="btn-thread-control mr-1">Pin</button>
                    @if(Gate::allows('manage', $thread))
                    <div class="relative" v-click-outside="hide">
                        <div @click="editing=true" class="btn-thread-control flex items-center "> <span
                                class="text-xl fas fa-ellipsis-h leading-none mr-3/2"></span>
                            <span class="fas fa-sort-down text-2xs leading-none pb-1"></span>
                        </div>
                        <div v-if="editing" class="absolute right-0 w-48 mt-2 shadow-lg">
                            <div class="px-4 py-2 border border-blue-light bg-white text-black-semi text-medium">More
                                Options
                            </div>
                            <div @click="edit"
                                class="px-4 py-2 cursor-pointer hover:bg-blue-light border border-t-0 border-blue-light bg-blue-lighter text-black-semi text-smaller">
                                Edit
                                Thread
                            </div>
                        </div>

                        <modal name="edit-thread" height='auto'>
                            <div class="form-container">
                                <div class="bg-blue-light text-lg text-black-semi border-b border-blue-light py-3 pl-3">
                                    Edit Thread
                                </div>
                                <form @submit.prevent="update">

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
                                        <button @click="hide" type="submit" class="form-button ">Save</button>
                                    </div>

                                </form>
                            </div>
                        </modal>
                    </div>
                    @endif
                </div>



                <replies :thread="thread"></replies>
            </main>
        </div>
    </thread>

</x-layouts._forum>