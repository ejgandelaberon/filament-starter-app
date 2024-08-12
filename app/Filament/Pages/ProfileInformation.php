<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Exception;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Js;
use JsonException;
use Throwable;
use function Filament\Support\is_app_url;

/**
 * @property Form $profileForm
 * @property Form $twoFactorAuthForm
 */
class ProfileInformation extends EditProfile
{
    public function getView(): string
    {
        return 'filament.pages.edit-profile';
    }

    /**
     * @throws Exception
     */
    protected function fillForm(): void
    {
        $data = $this->getUser()->attributesToArray();

        $this->callHook('beforeFill');

        $data = $this->mutateFormDataBeforeFill($data);

        $this->profileForm->fill($data);

        $this->callHook('afterFill');
    }

    /**
     * @throws Throwable
     */
    public function save(): void
    {
        try {
            $this->beginDatabaseTransaction();

            $this->callHook('beforeValidate');

            $data = $this->profileForm->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeSave($data);

            $this->callHook('beforeSave');

            $this->handleRecordUpdate($this->getUser(), $data);

            $this->callHook('afterSave');

            $this->commitDatabaseTransaction();
        } catch (Halt $exception) {
            $exception->shouldRollbackDatabaseTransaction() ?
                $this->rollBackDatabaseTransaction() :
                $this->commitDatabaseTransaction();

            return;
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        }

        if (request()->hasSession() && array_key_exists('password', $data)) {
            request()->session()->put([
                'password_hash_' . Filament::getAuthGuard() => $data['password'],
            ]);
        }

        $this->data['password'] = null;
        $this->data['passwordConfirmation'] = null;

        $this->getSavedNotification()?->send();

        if ($redirectUrl = $this->getRedirectUrl()) {
            $this->redirect($redirectUrl, navigate: FilamentView::hasSpaMode() && is_app_url($redirectUrl));
        }
    }

    public function save2fa(): void
    {
        dump('2fa');
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    protected function getForms(): array
    {
        return [
            'profileForm' => $this->getProfileForm(),
            'twoFactorAuthForm' => $this->form(
                $this->makeForm()
                    ->schema([
                        Section::make('Two-Factor Authentication')
                            ->aside()
                            ->description('Enable Two-Factor Authentication')
                            ->schema([])
                            ->footerActions([
                                Action::make('enable2fa')
                                    ->label('Enable')
                                    ->submit('save2fa')
                            ])
                    ])
                    ->operation('edit')
                    ->model($this->getUser())
                    ->statePath('data')
                    ->inlineLabel(!static::isSimple()),
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
    protected function getProfileForm(): Form
    {
        return $this->form(
            $this->makeForm()
                ->schema([
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
                                ->alpineClickHandler('document.referrer ? window.history.back() : (window.location.href = ' . Js::from(Filament::getUrl()) . ')')
                                ->color('gray')
                        ]),
                ])
                ->operation('edit')
                ->model($this->getUser())
                ->statePath('data')
                ->inlineLabel(!static::isSimple()),
        );
    }
}
