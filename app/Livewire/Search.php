<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Component;

class Search extends Component
{
    public $searchTerm = '';
    public $results;

    public function render()
    {
        if ($this->searchTerm == '') {
            $this->results = array();
        } else {
            $posts = Post::search($this->searchTerm)->get();
            $this->results = $posts;
        }
        
        return view('livewire.search');
    }
}
