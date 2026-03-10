<x-filament-panels::page.simple>
    <x-slot name="heading">
        {{ __('Verifikasi Dua Faktor') }}
    </x-slot>

    <x-slot name="subheading">
        {{ __('Masukkan kode 6-digit yang telah dikirim ke email Anda. Kode berlaku selama 24 jam.') }}
    </x-slot>

    <x-filament-panels::form wire:submit="verify">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="true"
        />
    </x-filament-panels::form>

    <div class="fi-simple-footer mt-4 flex items-center justify-between text-sm">
        <a
            href="{{ route('filament.sangkara.auth.login') }}"
            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition"
        >
            ← Kembali ke login
        </a>

        <span class="text-gray-400 dark:text-gray-500">•</span>

        <button
            type="button"
            wire:click="resendCode"
            wire:loading.attr="disabled"
            wire:target="resendCode"
            class="font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300 transition disabled:opacity-50"
        >
            <span wire:loading.remove wire:target="resendCode">Kirim ulang kode</span>
            <span wire:loading wire:target="resendCode">Mengirim...</span>
        </button>
    </div>
</x-filament-panels::page.simple>
