<p>{{ $thread->title }}</p>
@foreach($thread->replies as $reply)
<p id="#{{reply->id}}">{{ $reply->body }}</p>
@endforeach