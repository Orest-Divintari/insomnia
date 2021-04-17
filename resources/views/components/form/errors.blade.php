<div class="mt-2 mb-6">
    @if($errors->any())
    @foreach($errors->all() as $error)
    <div
        class="w-full py-4 bg-red-alert rounded mb-3 shadow-md border-l-2 border-red-alert-text font-light text-red-alert-text pl-5 flex items-center">
        <span class="fas fa-ban mr-3"></span>
        <p>{{ $error }}</p>
    </div>
    @endforeach
    @endif
</div>