<?php

namespace App\Observers;

use App\Models\GradeRecord;
use App\Notifications\NewGradeNotification;

class GradeRecordObserver
{
    public function created(GradeRecord $gradeRecord): void
    {
        $parent = $gradeRecord->student?->parentUser;

        $parent?->notify(new NewGradeNotification($gradeRecord));
    }
}
