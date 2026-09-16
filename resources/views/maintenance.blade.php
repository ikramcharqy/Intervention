<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Maintenance — {{ config('app.name', 'TechniTrack') }}</title>
    <style>
        body { margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center; background:#f8fafc; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; color:#131523; }
        .card { max-width:480px; margin:1.5rem; padding:2.5rem; background:#fff; border-radius:8px; box-shadow:0 1px 4px rgba(21,34,50,0.08); text-align:center; }
        .icon { width:48px; height:48px; margin:0 auto 1rem; color:#B98900; }
        h1 { font-size:1.25rem; font-weight:800; margin:0 0 0.75rem; }
        p { font-size:0.875rem; color:#5A607F; line-height:1.6; margin:0; }
    </style>
</head>
<body>
    <div class="card">
        <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" /></svg>
        <h1>Maintenance en cours</h1>
        <p>{{ $message }}</p>
    </div>
</body>
</html>
