<form wire:submit="save" class="ml-2 d-inline" action="/create-follow/{{$sharedData['username']}}" method="POST">
    @csrf
    <button class="btn btn-primary btn-sm">Follow <i class="fas fa-user-plus"></i></button>
</form>