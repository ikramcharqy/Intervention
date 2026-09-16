<?php

use App\Models\Intervention;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('intervention.{interventionId}.gps', function ($user, $interventionId) {
    if ($user->hasAnyRole(['admin', 'Super Admin', 'commercial'])) {
        return true;
    }

    $intervention = Intervention::find($interventionId);

    return $intervention && (int) $intervention->technicien_id === (int) $user->id;
});
