<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Mon Profil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('patch')

                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg mb-6">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-5">{{ __('Informations personnelles') }}</h3>
                    <div class="max-w-2xl">
                        @include('profile.partials.personal-info-fields')
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg mb-6">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-5">{{ __('Informations professionnelles') }}</h3>
                    <div class="max-w-2xl">
                        @include('profile.partials.professional-info-fields')
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg mb-6">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-5">{{ __('Préférences de notification') }}</h3>
                    <div class="max-w-2xl">
                        @include('profile.partials.notification-preferences-fields')
                    </div>
                </div>

                <div class="flex items-center gap-4 mb-6">
                    <x-primary-button>{{ __('Enregistrer') }}</x-primary-button>

                    @if (session('status') === 'profile-updated')
                        <p
                            x-data="{ show: true }"
                            x-show="show"
                            x-transition
                            x-init="setTimeout(() => show = false, 2000)"
                            class="text-sm text-gray-600 dark:text-gray-400"
                        >{{ __('Enregistré.') }}</p>
                    @endif
                </div>
            </form>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-5">{{ __('Sécurité du compte') }}</h3>
                <div class="max-w-xl space-y-8">
                    @include('profile.partials.update-password-form')
                    <div class="border-t border-gray-100 dark:border-gray-700 pt-8">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
