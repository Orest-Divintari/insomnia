<div class="flex items-center">
    <div class="mx-3">
        <a href="" class="text-gray-lightest text-xs hover:underline">{{ $item->date_updated }}</a>
        <a class="text-gray-lightest text-xs hover:underline"> {{ $item->poster->shortName }}</a>
    </div>
    <a href=""><img src=" {{ $poster->avatar_path }}" class="avatar w-6 h-6" alt=""></a>
</div>