<?php

namespace App\Notifications;

use App\Models\Attendance;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AbsenceNotification extends Notification
{
    use Queueable;

    public function __construct(public Attendance $attendance)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // TODO: إضافة قناة واتساب لاحقًا (whatsapp)
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $studentName = $this->attendance->student?->full_name ?? 'الطالب';
        $subjectName = $this->attendance->subject?->name ?? 'المادة';
        $date = optional($this->attendance->date)->format('Y-m-d');

        return [
            'type' => 'absence',
            'icon' => 'heroicon-o-x-circle',
            'color' => 'danger',
            'title' => 'إشعار غياب',
            'body' => "نود إعلامكم بغياب {$studentName} اليوم {$date} في مادة {$subjectName}.",
            'student_id' => $this->attendance->student_id,
        ];
    }
}
