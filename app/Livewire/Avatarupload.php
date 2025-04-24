<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;

class Avatarupload extends Component
{
    use WithFileUploads;

    public $avatar;

    public function save() {
        if (!auth()->check()) {
            abort(403, 'Unauthorized');
        }

        $user = auth()->user();

        $filename = $user->id . "-" . uniqid() . ".jpg";
        
        $manager = new ImageManager(new Driver());
        $image = $manager->read($this->avatar);
        $imgData = $image->cover(120,120)->toJpeg();
        Storage::disk('public')->put('avatars/' . $filename,$imgData);

        $oldAvatar = $user->avatar;

        $user->avatar = $filename;
        $user->save();

        if ($oldAvatar != '/fallback-avatar.jpg'){
            Storage::disk('public')->delete(str_replace("/storage/", "", $oldAvatar));
        }

        session()->flash('success', 'Congrats on the new avatar.');
        return $this->redirect('/manage-avatar', navigate: true);
    }

    public function render()
    {
        return view('livewire.avatarupload');
    }
}
