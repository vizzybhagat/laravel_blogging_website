<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use App\Events\OurExampleEvent;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\View;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;

class UserController extends Controller
{

    public function loginApi(Request $request){
        $incomingFields = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (auth()->attempt($incomingFields)) {
            //This returns an array and thats why we need to call the first().
            $user = User::where('username', $incomingFields['username'])->first();
            $token = $user->createToken('ourapptoken')->plainTextToken;
            return $token;
        }
        return 'sorry';
    }

    public function storeAvatar(Request $request){
        $request->validate([
            'avatar' => 'required|image|max:3000'
        ]);

        $user = auth()->user();

        $filename = $user->id . "-" . uniqid() . ".jpg";
        
        $manager = new ImageManager(new Driver());
        $image = $manager->read($request->file('avatar'));
        $imgData = $image->cover(120,120)->toJpeg();
        Storage::disk('public')->put('avatars/' . $filename,$imgData);

        $oldAvatar = $user->avatar;

        $user->avatar = $filename;
        $user->save();

        if ($oldAvatar != '/fallback-avatar.jpg'){
            Storage::disk('public')->delete(str_replace("/storage/", "", $oldAvatar));
        }

        return back()->with('success','Avatar successfully changed');

    }

    public function showAvatarForm(){
        return view('avatar-form');
    }

    private function getSharedData($pizza){
        $currentlyFollowing = 0;
        if (auth()->check()){
            $currentlyFollowing = Follow::where([['user_id','=',auth()->user()->id] ,['followeduser','=',$pizza->id]])->count();
        }

        View::share('sharedData',['currentlyFollowing' => $currentlyFollowing,'avatar' => $pizza->avatar,'username' => $pizza->username, 'postCount' => $pizza->posts()->count(),  'followerCount' => $pizza->followers()->count(),  'followingCount' => $pizza->followingTheseUsers()->count()]);
    }

    public function profile(User $pizza)
    {
        //$thePosts = $user->posts()->get();
        //return $thePosts;
        $this->getSharedData($pizza);
        return view('profile-posts', ['posts' => $pizza->posts()->latest()->get()]);
    }

    public function profileFollowers(User $pizza)
    {
        $this->getSharedData($pizza);
        return view('profile-followers', ['followers' => $pizza->followers()->latest()->get(), 'posts' => $pizza->posts()->latest()->get()]);
    }

    public function profileFollowing(User $pizza)
    {
        //$thePosts = $user->posts()->get();
        //return $thePosts;
        $this->getSharedData($pizza);
        return view('profile-following', ['following' => $pizza->followingTheseUsers()->latest()->get(),'posts' => $pizza->posts()->latest()->get()]);
    }

    public function showCorrectHomepage()
    {
        if (auth()->check()){
            return view('homepage-feed', ['posts' => auth()->user()->feedPosts()->latest()->paginate(4)]);
        } else {
            //function is called after 20 seconds
            $postCount = Cache::remember('postCount', 20, function() {
                //sleep(5);
                return Post::count();
            });
            return view('homepage',['postCount' => Post::count()]);
        }
    }

    public function logout()
    {
        event(new OurExampleEvent(['username' => auth()->user()->username, 'action' => 'logout']));
        auth()->logout();
        //event(new OurExampleEvent());
        return redirect('/')->with('success','You have successfully logged out');
    }

    public function register(Request $request)
    {
        $incomingFields = $request->validate([
            'username' => ['required', 'min:3', 'max:20', Rule::unique('users','username')],
            'email' => ['required','email',Rule::unique('users','email')],
            'password' => ['required','min:8','confirmed'],
        ]);
        $user = User::create($incomingFields);
        auth()->login($user);
        return redirect('/')->with('success','Thank you for creating an account');
    }

    public function login(Request $request)
    {
        $incomingFields = $request->validate([
            'loginusername' => 'required',
            'loginpassword' => 'required'
        ]);

        if (auth()->attempt(['username'=> $incomingFields['loginusername'], 'password'=>$incomingFields['loginpassword']])){
            $request->session()->regenerate();
            //event(new OurExampleEvent());
            event(new OurExampleEvent(['username' => auth()->user()->username, 'action' => 'login']));
            return redirect('/')->with('success','You have sucessfully logged in');
        } else {
            return redirect('/')->with('failure','Invalid Login');
        }
    }
}
