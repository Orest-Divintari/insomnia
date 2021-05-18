@if($errors->any())
<x-errors.title></x-errors.title>
@foreach($errors->all() as $error)
<x-errors.message :message="$error"></x-errors.message>
@endforeach
@endif