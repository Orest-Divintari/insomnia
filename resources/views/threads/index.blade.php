@foreach($threads as $thread)
<p>{{$thread->title}}</p>
<a href="/threads/{{$thread->slug}}"> {{ $thread->title }} </a>
@endforeach