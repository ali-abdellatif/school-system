<?php

namespace App\Observers;

use App\Models\Attendance;
use App\Notifications\AbsenceNotification;

class AttendanceObserver
{
    public function created(Attendance $attendance): void
    {
        if ($attendance->status !== 'absent') {
            return;
        }

        $parent = $attendance->student?->parentUser;

        $parent?->notify(new AbsenceNotification($attendance));
    }
}
