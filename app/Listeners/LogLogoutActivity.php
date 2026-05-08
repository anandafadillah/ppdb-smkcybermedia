<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Logout;

class LogLogoutActivity
{
    public function handle(Logout $event): void
    {
        if ($event->user) {
            ActivityLog::create([
                'user_id'    => $event->user->id,
                'event'      => 'logout',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }
}
