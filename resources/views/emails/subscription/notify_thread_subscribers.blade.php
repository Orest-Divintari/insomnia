<div class="text-xs">
    <a href="" class="text-blue-mid">{{ $reply->poster->name }}</a> replied to a thread you are watching at <a href=""
        class="text-blue-mid">Insomnia Forum</a>
</div>
<div>
    <h1 class="text-blue-mid text-2xl">{{ $thread->title }}</h1>
    <p class="border-l border-blue-mid text-xs">
        {{ $reply->body }}
    </p>
</div>
<div class="form-button-container">
    <button class="form-button"> View this thread </button>
</div>
<p>Please do not reply to his messasge</p>