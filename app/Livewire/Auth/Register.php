<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\Country;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        
        // Get Ghana as default country for web users, or create if doesn't exist
        $country = Country::firstOrCreate(
            ['code' => 'GH'],
            [
                'name' => 'Ghana',
                'currency_code' => 'GHS',
                'currency_symbol' => 'GH₵',
                'currency_name' => 'Ghana Cedi',
                'is_active' => true,
            ]
        );
        
        $validated['country_id'] = $country->id;
        $validated['user_type'] = 'customer';

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}
