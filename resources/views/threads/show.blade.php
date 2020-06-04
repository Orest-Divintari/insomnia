<p>{{ $thread->title }}</p>
@foreach($thread->replies as $reply)
<p>{{ $reply->body }}</p>
@endforeach