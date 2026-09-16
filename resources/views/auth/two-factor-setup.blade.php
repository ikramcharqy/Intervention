<x-guest-layout>
    <div class="max-w-md mx-auto space-y-6">
        <div class="text-center">
            <h2 class="text-lg font-bold text-gray-900">Activation obligatoire de la 2FA</h2>
            <p class="text-sm text-gray-600 mt-2">
                Le rôle Super Admin/Admin exige l'authentification à deux facteurs. Scannez ce QR code avec
                Google Authenticator, Microsoft Authenticator ou une application TOTP équivalente, puis
                saisissez le code à 6 chiffres généré pour confirmer l'activation.
            </p>
        </div>

        <div class="flex justify-center bg-white p-4 rounded-lg border [&_svg]:w-48 [&_svg]:h-48">
            {!! $qrCodeSvg !!}
        </div>

        @if ($errors->any())
            <div class="text-sm text-red-600 text-center">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('two-factor.confirm') }}" class="space-y-4">
            @csrf
            <input type="text" name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="6"
                   placeholder="Code à 6 chiffres" required autofocus
                   class="w-full text-center text-lg tracking-widest px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
            <button type="submit" class="w-full py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-lg">
                Confirmer et activer
            </button>
        </form>

        <p class="text-[11px] text-center text-gray-400">
            Après confirmation, des codes de secours à usage unique vous seront présentés une seule fois — ils
            permettent de récupérer l'accès en cas de perte de votre application d'authentification.
        </p>
    </div>
</x-guest-layout>
