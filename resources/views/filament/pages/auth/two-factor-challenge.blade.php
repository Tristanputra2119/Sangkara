<x-filament-panels::page.simple>
    <x-slot name="heading">
        {{ __('Verifikasi Dua Faktor') }}
    </x-slot>

    <x-slot name="subheading">
        {{ __('Masukkan kode 6-digit yang telah dikirim ke email Anda. Kode berlaku selama 24 jam.') }}
    </x-slot>

    <form wire:submit="verify">
        {{ $this->form }}

        <x-filament::button
            type="submit"
            size="lg"
            class="w-full"
            wire:loading.attr="disabled"
            wire:target="verify"
        >
            <span wire:loading.remove wire:target="verify">Verifikasi</span>
            <span wire:loading wire:target="verify">Memverifikasi...</span>
        </x-filament::button>
    </form>

    <div class="fi-simple-footer mt-4 flex items-center justify-between text-sm">
        <x-filament::link
            href="{{ route('filament.sangkara.auth.login') }}"
            color="gray"
        >
            &larr; Kembali ke login
        </x-filament::link>

        <span class="text-gray-400 dark:text-gray-500">•</span>

        <x-filament::button
            type="button"
            wire:click="resendCode"
            wire:loading.attr="disabled"
            wire:target="resendCode"
            color="primary"
            variant="text"
            size="sm"
        >
            <span wire:loading.remove wire:target="resendCode">Kirim ulang kode</span>
            <span wire:loading wire:target="resendCode">Mengirim...</span>
        </x-filament::button>
    </div>
</x-filament-panels::page.simple>
