<form wire:submit="save" class="ml-2 d-inline" action="/remove-follow/{{$sharedData['username']}}" method="POST">
    @csrf
    <button class="btn btn-danger btn-sm">Stop Following <i class="fas fa-user-times"></i></button>
</form>