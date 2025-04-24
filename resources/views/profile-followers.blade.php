<x-profile :sharedData="$sharedData" doctitle="{{$sharedData['username']}}'s Followers'">
    <div class="list-group">
      @foreach($followers as $follow)
      <a href="/profile/{{$follow->userDoingTheFollowing->username}}" class="list-group-item list-group-item-action">
        <img class="avatar-tiny" src="{{$follow->userDoingTheFollowing->avatar}}" />
        {{$follow->userDoingTheFollowing->username}}
        {{-- <strong>{{$post->title}}</strong> on {{$post->created_at->format('n/j/Y')}} --}}
      </a>
      @endforeach
    </div>
  </x-profile>