<form wire:submit="create" action="/create-post" method="POST">
    @csrf
    <div class="form-group">
      <label for="post-title" class="text-muted mb-1"><small>Title</small></label>
      <input wire:model="title" value="{{old('title')}}" name="title" id="post-title" class="form-control form-control-lg form-control-title" type="text" placeholder="" autocomplete="off" />
      @error('title')
      <p class='m-0 small alert alert-danger shadow-sm'>{{$message}}</p>
      @enderror
  
    </div>

    <div class="form-group">
      <label for="post-body" class="text-muted mb-1"><small>Body Content</small></label>
      <textarea wire:model="body" name="body" id="post-body" class="body-content tall-textarea form-control" type="text">{{old('body')}}</textarea>
    </div>

    <button class="btn btn-primary">Save New Post</button>
</form>