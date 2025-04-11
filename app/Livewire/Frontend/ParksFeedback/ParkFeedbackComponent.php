<?php

namespace App\Livewire\Frontend\ParksFeedback;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\ParkFeedback;
use App\Models\ParkCoolnessVote;

class ParkFeedbackComponent extends Component
{
    public $parkId;
    public $rating = 0;
    public $comment = '';
    public $coolness = 5;

    protected $listeners = ['openParkFeedback'];

    public function openParkFeedback($parkId)
    {
        $this->parkId = $parkId;
    }

    #[On('openParkFeedback')]
    public function setParkId($id)
    {
        $this->parkId = $id;
    }


    public function submitFeedback() {
        ParkFeedback::create([
            'park_id' => $this->parkId,
            'rating' => $this->rating,
            'comment' => $this->comment,
        ]);

        $this->reset(['rating', 'comment']);
        session()->flash('message', 'Danke für deine Bewertung!');
    }

    public function submitCoolness() {
        ParkCoolnessVote::create([
            'park_id' => $this->parkId,
            'value' => $this->coolness,
            'ip_address' => request()->ip(),
        ]);

        session()->flash('coolnessMessage', 'Danke für deine Coolness-Stimme!');
    }

    public function render()
    {
        return view('livewire.frontend.parks-feedback.park-feedback-component');
    }
}
