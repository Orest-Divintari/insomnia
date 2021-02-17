<p class="text-smaller text-gray-lightest"> {{ $activity->description }} <a class="blue-link italic"
        href="{{ route('profiles.show', $activity->subject ) }}">
        {{ $activity->subject->name }} </a> </p>