<x-guest-layout>
    <div class="max-w-md mx-auto space-y-6">
        <div class="text-center">
            <h2 class="text-lg font-bold text-gray-900">2FA activée — Codes de secours</h2>
            <p class="text-sm text-red-600 font-semibold mt-2">
                Sauvegardez ces codes maintenant : ils ne seront plus jamais affichés. Chacun ne peut
                être utilisé qu'une seule fois, en cas de perte d'accès à votre application d'authentification.
            </p>
        </div>

        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <div class="grid grid-cols-2 gap-2 font-mono text-sm text-gray-800" id="recovery-codes">
                @foreach($codes as $code)
                    <div class="bg-white border border-gray-200 rounded px-3 py-2 text-center">{{ $code }}</div>
                @endforeach
            </div>
        </div>

        <div class="flex gap-3">
            <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('recovery-codes').innerText.trim().replace(/\n+/g, '\n'))"
                    class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg">
                Copier les codes
            </button>
            <a href="{{ route('superadmin.dashboard') }}" class="flex-1 text-center py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold rounded-lg">
                J'ai sauvegardé mes codes
            </a>
        </div>
    </div>
</x-guest-layout>
