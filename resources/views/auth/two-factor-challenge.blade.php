<x-guest-layout>
    <div class="max-w-md mx-auto space-y-6">
        <div class="text-center">
            <h2 class="text-lg font-bold text-gray-900">Vérification en deux étapes</h2>
            <p class="text-sm text-gray-600 mt-2">
                Saisissez le code à 6 chiffres généré par votre application d'authentification, ou l'un de
                vos codes de secours à usage unique si vous avez perdu l'accès à celle-ci.
            </p>
        </div>

        @if ($errors->any())
            <div class="text-sm text-red-600 text-center">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('two-factor.verify') }}" class="space-y-4">
            @csrf
            <input type="text" name="code" autocomplete="one-time-code" maxlength="12"
                   placeholder="Code à 6 chiffres ou code de secours" required autofocus
                   class="w-full text-center text-lg tracking-widest px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
            <button type="submit" class="w-full py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-lg">
                Vérifier
            </button>
        </form>
    </div>
</x-guest-layout>
