@foreach($subCategories as $subCategory)
<a href="/forum/categories/{{ $subCategory->slug }}"> {{ $subCategory->title }} </a>
@endforeach