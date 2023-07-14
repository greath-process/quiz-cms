<?php


namespace App\Http\Livewire\Auth;

use App\Models\InvitationCode;
use Closure;
use Filament\Facades\Filament;
use JeffGreco13\FilamentBreezy\Http\Livewire\Auth\Register as FilamentBreezyRegister;
use Filament\Forms;
use Illuminate\Auth\Events\Registered;
use Spatie\Permission\Contracts\Role;
use Spatie\Permission\Models\Role as ModelsRole;

class Register extends FilamentBreezyRegister
{
    // Define the new attributes
    public $invitation_code;

    // Override the getFormSchema method and merge the default fields then add your own.
    protected function getFormSchema(): array
    {
        return array_merge(parent::getFormSchema(), [
            Forms\Components\TextInput::make("invitation_code")
                ->label("Invitation Code")
                ->required()
                ->rules(
                    [
                        function () {
                            return function (string $attribute, $value, Closure $fail) {
                                if (!InvitationCode::where('code', $value)->exists()) {
                                    $fail("The code is invalid.");
                                }
                            };
                        },
                    ]
                ),
        ]);
    }

    // Use this method to modify the preparedData before the register() method is called.
    protected function prepareModelData($data): array
    {
        $preparedData = parent::prepareModelData($data);
        // $preparedData["invitation_code"] = $this->invitation_code;

        return $preparedData;
    }

    // Optionally, you can override the entire register() method to customize exactly what happens at registration
    public function register()
    {
        $preparedData = $this->prepareModelData($this->form->getState());
        $user = config('filament-breezy.user_model')::create($preparedData);
        if ($role = ModelsRole::where('name', 'Guest Quizmaster')->first()) {
            $user->assignRole($role);
        }
        try {
            event(new Registered($user));
        } catch (\Exception $e) {
            return redirect()->to(config('filament-breezy.registration_redirect_url'));
        }
        Filament::auth()->login($user, true);

        return redirect()->to(config('filament-breezy.registration_redirect_url'));
    }
}
