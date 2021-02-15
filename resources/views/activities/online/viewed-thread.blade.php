<p class="text-smaller text-gray-lightest"> {{ $activity->description }} <a class="blue-link italic"
        href="{{ route('threads.show', $activity->subject ) }}">
        {{ $activity->subject->title }} </a> </p>