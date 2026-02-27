<?php

namespace App\Livewire\Backend\Settings;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class AccountSettings extends Component
{
    public $name;
    public $email;
    public $current_password;
    public $password;
    public $password_confirmation;

    protected function admin()
    {
        return Auth::guard('admin')->user();
    }

    public function mount()
    {
        $admin = $this->admin();

        $this->name  = $admin->name;
        $this->email = $admin->email;
    }

    public function updateProfile()
    {
        $admin = $this->admin();

        $this->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('admins')->ignore($admin->id),
            ],
        ]);

        $admin->update([
            'name'  => $this->name,
            'email' => $this->email,
        ]);

        session()->flash('success', 'Profil erfolgreich aktualisiert.');
    }

    public function updatePassword()
    {
        $admin = $this->admin();

        $this->validate([
            'current_password' => ['required'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($this->current_password, $admin->password)) {
            $this->addError('current_password', 'Aktuelles Passwort ist falsch.');
            return;
        }

        $admin->update([
            'password' => Hash::make($this->password),
        ]);

        $this->reset([
            'current_password',
            'password',
            'password_confirmation'
        ]);

        session()->flash('success', 'Passwort erfolgreich geändert.');
    }

    public function render()
    {
        return view('livewire.backend.settings.account-settings')
            ->layout('raadmin.layout.master');
    }
}
