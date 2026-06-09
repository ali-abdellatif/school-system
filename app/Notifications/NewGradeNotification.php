<?php

namespace App\Notifications;

use App\Models\GradeRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewGradeNotification extends Notification
{
    use Queueable;

    public function __construct(public GradeRecord $gradeRecord)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $studentName = $this->gradeRecord->student?->full_name ?? 'الطالب';
        $subjectName = $this->gradeRecord->subject?->name ?? 'المادة';
        $score = rtrim(rtrim((string) $this->gradeRecord->score, '0'), '.');
        $max = rtrim(rtrim((string) $this->gradeRecord->max_score, '0'), '.');

        return [
            'type' => 'grade',
            'icon' => 'heroicon-o-chart-bar',
            'color' => 'info',
            'title' => 'رصد درجة جديدة',
            'body' => "تم رصد درجة {$studentName} في مادة {$subjectName}: {$score}/{$max}.",
            'student_id' => $this->gradeRecord->student_id,
        ];
    }
}
