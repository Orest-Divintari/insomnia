<x-layouts.forum>
    <x-errors.title></x-errors.title>
    <x-errors.message :message="$exception->getMessage() ?: 'Forbidden'"></x-errors.message>
</x-layouts.forum>