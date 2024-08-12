<x-filament-panels::page>
    <x-filament-panels::form id="profileForm" wire:submit="save">
        {{ $this->profileForm }}
    </x-filament-panels::form>

    <x-filament-panels::form id="twoFactorAuthForm" wire:submit="save2fa">
        {{ $this->twoFactorAuthForm }}
    </x-filament-panels::form>
</x-filament-panels::page>
