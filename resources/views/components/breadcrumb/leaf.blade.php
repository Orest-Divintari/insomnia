<div class="flex text-sm text-gray-lightest items-center mr-4">
    <li class="">
        @if(isset($jumpToContent) && $jumpToContent == 'false' )
        <a href="{{ $route }}" class="hover:underline font-bold">{{ $title }}</a>
        @else
        <a href="{{ $route . '#' . $title }}" class="hover:underline font-bold">{{ $title }}</a>
        @endif
    </li>
</div>