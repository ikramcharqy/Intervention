@if(auth()->user()->hasAnyRole(['Super Admin', 'admin']))
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Supprimer le compte') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Une fois votre compte supprimé, toutes ses ressources et données seront définitivement supprimées. Avant de supprimer votre compte, veuillez télécharger toute donnée ou information que vous souhaitez conserver.') }}
            </p>
        </header>

        <x-danger-button
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        >{{ __('Supprimer le compte') }}</x-danger-button>

        <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
            <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                @csrf
                @method('delete')

                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('Êtes-vous sûr de vouloir supprimer votre compte ?') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Une fois votre compte supprimé, toutes ses ressources et données seront définitivement supprimées. Veuillez saisir votre mot de passe pour confirmer que vous souhaitez supprimer définitivement votre compte.') }}
                </p>

                <div class="mt-6">
                    <x-input-label for="password" value="{{ __('Mot de passe') }}" class="sr-only" />

                    <x-text-input
                        id="password"
                        name="password"
                        type="password"
                        class="mt-1 block w-3/4"
                        placeholder="{{ __('Mot de passe') }}"
                    />

                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Annuler') }}
                    </x-secondary-button>

                    <x-danger-button class="ms-3">
                        {{ __('Supprimer le compte') }}
                    </x-danger-button>
                </div>
            </form>
        </x-modal>
    </section>
@else
    {{-- Un Commercial/Technicien ne peut pas supprimer son propre compte en self-service :
    la désactivation d'un compte employé est une action Admin/Super Admin, depuis la
    gestion des utilisateurs. Il peut en revanche en faire la demande. --}}
    <section class="space-y-4">
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Désactivation du compte') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('La suppression ou la désactivation d\'un compte employé est réservée aux administrateurs. Vous pouvez toutefois en faire la demande — les administrateurs seront notifiés.') }}
            </p>
        </header>

        <form method="post" action="{{ route('profile.requestDeactivation') }}" onsubmit="return confirm('Confirmez-vous l\'envoi d\'une demande de désactivation de votre compte aux administrateurs ?');">
            @csrf
            <x-secondary-button type="submit">
                {{ __('Demander la désactivation de mon compte') }}
            </x-secondary-button>
        </form>

        @if (session('status') === 'deactivation-requested')
            <p class="text-sm text-emerald-600 dark:text-emerald-400">{{ __('Votre demande a été envoyée aux administrateurs.') }}</p>
        @endif
    </section>
@endif
