<x-layouts.account section="ignoring" title="Ignoring">
    <x-slot name="main">

        <div class="flex text-sm tetx-gray-shuttle">
            <a href="{{ route('account.ignored-users.index') }}"
                class="py-7/2 px-3 {{ $ignoredType == 'members' ? 'text-brown-fuzzy border-b-2 border-brown-fuzzy' : '' }}">Members</a>
            <a href="{{ route('account.ignored-threads.index') }}"
                class="py-7/2 px-3 {{ $ignoredType == 'threads' ? 'text-brown-fuzzy border-b-2 border-brown-fuzzy ' : '' }}">Threads</a>
        </div>
        <hr class="mb-5/2">
        {{ $slot }}
    </x-slot>
</x-layouts.account>