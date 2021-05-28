<x-layouts.forum>
    @push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.2.3/trix.css">
    @endpush
    <conversation :conversation="{{ $conversation }}" inline-template v-cloak>
        <div>
            <header>
                <h1 class="font-bold text-3xl mb-1" v-text="title"></h1>
                <div class="flex items-center text-smaller text-gray-lightest">
                    <span class="fas fa-user mr-1"></span>
                    @foreach($conversation->participants as $participant)
                    <p>
                        {{ $participant->name }}
                    </p>
                    @if(!$loop->last)
                    <p class="mr-1">, </p>
                    @endif
                    @endforeach

                    <p class=" dot"></p>
                    <span class="mr-1 fas fa-clock"></span>
                    <a href="{{route('conversations.show', $conversation->slug)}}" class="mr-1 hover:underline">
                        {{ $conversation->date_created }}</a>
                </div>
            </header>

            <main class="section">
                <x-breadcrumb.container>
                    <x-breadcrumb.item :title="'Forum'" :route="route('forum')">
                    </x-breadcrumb.item>
                    <x-breadcrumb.leaf :title="'Conversations'" :route="route('conversations.index')">
                    </x-breadcrumb.leaf>
                </x-breadcrumb.container>

                <div class="flex">
                    <div class="flex-1">
                        <div class="mt-7 flex justify-end">
                            @if(Gate::allows('update', $conversation))
                            <button @click="showEditModal" class="btn-white-blue mr-1">Edit</button>
                            <modal name="edit-conversation" height='auto' width="50%">
                                <div class="form-container">
                                    <div
                                        class="flex justify-between items-center bg-blue-light text-lg text-black-semi border-b border-blue-light py-3 px-3">
                                        <p> Edit Conversation </p>
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
                                                        name="title" autocomplete="title">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-left-col"></div>
                                            <div class="form-right-col">
                                                <div class="flex flex-row-reverse items-center">
                                                    <label for="locked" class="form-label flex-1 ml-2">
                                                        Lock conversation</label>

                                                    <input :checked="locked" ref="lock" type="checkbox"
                                                        id="lock_conversation" name="locked" />
                                                </div>
                                                <p class="text-gray-lightest text-xs">No responses will be
                                                    allowed
                                                </p>
                                            </div>
                                        </div>
                                        <div class="form-button-container justify-center">
                                            <button @click="update" type="button" class="form-button ">Save</button>
                                        </div>

                                    </form>
                                </div>
                            </modal>
                            @endif
                            <button v-if="starred" @click="toggleStar" class="btn-white-blue mr-1">Unstar</button>
                            <button v-else @click="toggleStar" class="btn-white-blue mr-1">Star</button>
                            <button @click="toggleRead" v-if="isRead" class="btn-white-blue mr-1">Mark unread</button>
                            <button @click="toggleRead" v-else class="btn-white-blue mr-1">Mark read</button>
                            <button @click="showLeaveModal" class="btn-white-blue mr-1">Leave</button>
                            <modal name="leave-conversation" width="48%" height='auto'>
                                <div class="form-container">
                                    <div
                                        class="flex justify-between items-center bg-blue-light text-lg text-black-semi border-b border-blue-light py-3 px-3">
                                        <p> Leave Conversation </p>
                                        <button @click="hideLeaveModal" class="fas fa-times"></button>
                                    </div>
                                    <p class="text-smaller text-black-semi p-4">
                                        Leaving a conversation will remove it from your conversation list.
                                    </p>
                                    <!-- ROW -->
                                    <div class="form-row">
                                        <!-- LEFT -->
                                        <div class="form-left-col">
                                            <label class="form-label" for="leave">Future message handling:</label>
                                        </div>
                                        <!-- RIGHT -->
                                        <div class="form-right-col border-t border-white-catskill">
                                            <p class="form-label-phone">Future message handling:</p>
                                            <div class="flex flex-row-reverse items-center mt-5/2">
                                                <div class="flex-1 flex flex-col">
                                                    <p class="text-sm text-black-semi flex-1">Allow future messages</p>
                                                </div>
                                                <input class="form-input w-3 mr-2 mt-1/2" type="radio" id="leave"
                                                    name="leave" ref="hide" checked>
                                            </div>
                                            <p class="ml-5 text-xs text-gray-lightest">Should this conversation receive
                                                further responses in the future, this conversation will be restored
                                                to your inbox.
                                            </p>
                                            <div class="flex flex-row-reverse items-center mt-3">
                                                <div class="flex-1 flex flex-col">
                                                    <p class="text-sm text-black-semi flex-1">Ignore future messages</p>
                                                </div>
                                                <input class="form-input w-3 mr-2 mt-1/2" type="radio" id="leave"
                                                    name="leave" ref="leave">
                                            </div>
                                            <p class="ml-5 text-xs text-gray-lightest">You will not be notified of any
                                                future responses and the conversation will remain deleted.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="form-button-container justify-center">
                                        <button @click="leave" type="button" class="form-button ">Leave</button>
                                    </div>

                                </div>
                            </modal>
                        </div>

                        <replies :repliable="conversation" :replies="{{ json_encode($messages) }}"></replies>
                    </div>
                    <div class="h-0 mt-12">
                        <x-conversations.info :messages="$messages" :participants="$conversation->participants">
                        </x-conversations.info>
                        <x-conversations.participants :conversation="$conversation"
                            :participants="$conversation->participants">
                        </x-conversations.participants>

                        @can('manage', $conversation)
                        <invite-participants-modal></invite-participants-modal>
                        @endcan
                    </div>

                </div>
            </main>

        </div>
    </conversation>

</x-layouts.forum>