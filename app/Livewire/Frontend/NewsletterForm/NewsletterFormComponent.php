<?php

namespace App\Livewire\Frontend\NewsletterForm;

use Livewire\Component;
use App\Models\NewsletterSubscriber;

class NewsletterFormComponent extends Component
{

    public $email;

    public function subscribe()
    {
        $this->validate([
            'email' => 'required|email|unique:newsletter_subscribers,email',
        ]);

        try {
            NewsletterSubscriber::create(['email' => $this->email]);
            $this->email = '';
            $this->dispatch('alert', ['type' => 'success', 'message' => 'Vielen Dank für die Anmeldung!']);
        } catch (\Exception $e) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Es ist ein Fehler aufgetreten!']);
        }
    }

    public function render()
    {
        return view('livewire.frontend.newsletter-form.newsletter-form-component');
    }
}
