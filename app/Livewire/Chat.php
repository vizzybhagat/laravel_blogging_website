<?php

namespace App\Livewire;

use Livewire\Component;
use App\Events\ChatMessage;

class Chat extends Component
{

    public $textvalue = '';
    public $chatLog = array();

    public function getListeners() {
        return [
            "echo-private:chatchannel,ChatMessage" => 'notifyNewMessage'
        ];
    }

    public function notifyNewMessage($x) {
        array_push($this->chatLog, $x['chat']);
    }

    public function send() {
        if (!auth()->check()) {
            abort(403, 'Unauthorized');
        }

        if (trim(strip_tags($this->textvalue)) == "") {
            return;
        }

        array_push($this->chatLog, ['selfmessage' => true, 'username' => auth()->user()->username, 'textvalue' => strip_tags($this->textvalue), 'avatar' => auth()->user()->avatar]);
        broadcast(new ChatMessage(['selfmessage' => false, 'username' => auth()->user()->username, 'textvalue' => strip_tags($this->textvalue), 'avatar' => auth()->user()->avatar]))->toOthers();
        $this->textvalue = '';
    }

    public function render()
    {
        return view('livewire.chat');
    }
}
