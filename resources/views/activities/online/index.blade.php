<x-layouts.forum>
    <header>
        <h1 class="section-title"> Current Visitors </h1>
    </header>

    <main class="section flex">
        <div class="w-4/5">
            <div class="border rounded border-white-catskill">
                <div class="flex border-b-6 border-white-catskill">
                    <a class="tab text-smaller py-3 px-4  {{ ( $type != 'member' && $type != 'guest') ? 'is-active' : 'text-gray-lightest'}} "
                        href="{{ route('online-user-activities.index') }}">Everyone</a>
                    <a class="tab text-smaller py-3 px-4  {{ $type == 'member' ? 'is-active' : 'text-gray-lightest' }} "
                        href="{{ route('online-user-activities.index', [ 'type' => 'member'] ) }}">Members</a>
                    <a class="tab text-smaller py-3 px-4  {{ $type == 'guest' ? 'is-active' : 'text-gray-lightest' }} "
                        href="{{ route('online-user-activities.index', [ 'type' => 'guest'] ) }}">Guests</a>
                </div>
                @forelse($activities as $activity)
                @can('view_current', [App\Activity::class, $activity->user])
                <div class="flex items-start p-7/2 {{ $loop->last ? '' : 'border-b' }}">
                    <div class="">
                        @if(isset($activity->user))
                        <profile-popover :user=" {{ $activity->user }} " trigger="avatar"
                            trigger-classes="avatar-lg mt-1">
                        </profile-popover>
                        @else
                        <img src="{{ guest_avatar() }}" class="avatar-lg" alt="asds">
                        @endif
                    </div>
                    <div class="leading-snug pl-7/2">
                        @if(isset($activity->user))
                        <profile-popover popover-classes="leading-normal" :user=" {{ $activity->user }}"
                            trigger-classes="text-md blue-link font-bold">
                        </profile-popover>
                        @else
                        <p class="text-md text-black font-bold">Guest</p>
                        @endif
                        @if(isset($activity->user))
                        <p class="text-smaller text-black-semi">Macrumors newbie</p>
                        @endif
                        <div class="flex items-center">
                            @include( 'activities.online.' . $activity->type)
                            <p class="dot"></p>
                            <p class="text-smaller text-gray-lightest"> {{ $activity->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                </div>
                @endcan
                @empty
                <p class="font-bold p-5">There are no activities yet</p>
                @endforelse
            </div>
            <paginator :dataset="{{ json_encode($activities) }}"></paginator>
        </div>
        <div class="flex-1">
            @include('activities.online.statistics')
        </div>

    </main>
</x-layouts.forum>