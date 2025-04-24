<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Mail\NewPostEmail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\SendNewPostEmail;
use Illuminate\Support\Facades\Mail;

class PostController extends Controller
{

    public function search($term){
        $posts = Post::search($term)->get();
        $posts->load('user:id,username,avatar');
        return $posts;
        //return Post::where('title', 'LIKE', '%' . $term . '%')->orWhere('body', 'LIKE', '%' . $term . '%')->with('user:id,username,avatar')->get();
    }

    public function actuallyUpdate(Post $post,Request $request)
    {
        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);

        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);

        $post->update($incomingFields);

        return back()->with('success','Post successfully updated.');
    }

    public function showEditForm(Post $post)
    {
        return view('edit-post',['post' => $post]);
    }

    public function delete(Post $post)
    {
        if (auth()->user()->cannot('delete',$post))
        {
            return 'You cannot do that';
        }
        $post->delete();

        return redirect('/profile/'.auth()->user()->username)->with('success','Post successfully deleted');
    }

    public function deleteApi(Post $post)
    {
        if (auth()->user()->cannot('delete',$post))
        {
            return 'You cannot do that';
        }
        $post->delete();

        return true;
    }

    public function viewSinglePost(Post $post){
        // if($post->user_id === auth()->user()->id){
        //     return 'You are the author';
        // }
        // return 'You are not the author';
        $post['body'] = Str::markdown($post->body);
        return view('single-post',['post' => $post]);
    }

    public function storeNewPost(Request $request){
        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);

        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);
        $incomingFields['user_id'] = auth()->id();
                
        $newPost = Post::create($incomingFields);
        
        dispatch(new SendNewPostEmail(['sendTo' => auth()->user()->email, 'name' => auth()->user()->username, 'title' => $newPost->title]));

        return redirect("/post/{$newPost->id}")->with('success','New post created succesfully');

    }

    public function storeNewPostApi(Request $request){
        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);

        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);
        $incomingFields['user_id'] = auth()->id();
                
        $newPost = Post::create($incomingFields);
        
        dispatch(new SendNewPostEmail(['sendTo' => auth()->user()->email, 'name' => auth()->user()->username, 'title' => $newPost->title]));

        return $newPost->id;

    }

    public function showCreateForm(){
        return view('create-post');
    }
}
