<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\InteractsWithBrowserSessions;
use App\Filament\Pages\Concerns\InteractsWithTwoFactorAuthentication;
use Exception;
use Filament\Actions\Action;
use Filament\Auth\Pages\EditProfile;
use Filament\Facades\Filament;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Js;
use Laravel\Jetstream\ConfirmsPasswords;

/**
 * @property Form $twoFactorAuthForm
 * @property Form $browserSessionsForm
 */
class ProfileInformation extends EditProfile
{
    use ConfirmsPasswords;
    use InteractsWithBrowserSessions;
    use InteractsWithTwoFactorAuthentication;

    public function getView(): string
    {
        return 'filament.pages.edit-profile';
    }

    /**
     * @return array<string, Schema>
     *
     * @throws Exception
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->getProfileForm(),
            'twoFactorAuthForm' => $this->form(
                $this->makeSchema()
                    ->components($this->getTwoFactorAuthenticationFormFields())
                    ->operation('edit')
                    ->model($this->getUser())
                    ->inlineLabel(! static::isSimple()),
            ),
            'browserSessionsForm' => $this->form(
                $this->makeSchema()
                    ->components($this->getBrowserSessionsFormFields())
                    ->operation('edit')
                    ->model($this->getUser())
                    ->inlineLabel(! static::isSimple()),
            ),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    /**
     * @throws Exception
     */
    protected function getProfileForm(): Schema
    {
        return $this->form(
            $this->makeSchema()
                ->components([
                    Section::make('Profile Information')
                        ->aside()
                        ->description('Update your profile information and change your password.')
                        ->schema([
                            $this->getNameFormComponent(),
                            $this->getEmailFormComponent(),
                            $this->getPasswordFormComponent(),
                            $this->getPasswordConfirmationFormComponent(),
                        ])
                        ->footerActions([
                            Action::make('save')
                                ->label('Save Changes')
                                ->submit('save')
                                ->keyBindings(['mod+s']),

                            Action::make('back')
                                ->label('Cancel')
                                ->alpineClickHandler('document.referrer ? window.history.back() : (window.location.href = '.Js::from(Filament::getUrl()).')')
                                ->color('gray'),
                        ]),
                ])
                ->operation('edit')
                ->model($this->getUser())
                ->statePath('data')
                ->inlineLabel(! static::isSimple()),
        );
    }
}
