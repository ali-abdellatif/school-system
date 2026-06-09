<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ApplicationStatusNotification extends Notification
{
    use Queueable;

    public function __construct(public Application $application)
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
        $name = $this->application->full_name;
        $statusLabel = [
            'pending' => 'قيد الانتظار',
            'reviewing' => 'قيد المراجعة',
            'approved' => 'مقبول',
            'rejected' => 'مرفوض',
        ][$this->application->status] ?? $this->application->status;

        return [
            'type' => 'application_status',
            'icon' => 'heroicon-o-document-text',
            'color' => $this->application->status === 'approved' ? 'success' : ($this->application->status === 'rejected' ? 'danger' : 'warning'),
            'title' => 'تحديث حالة طلب القبول',
            'body' => "تم تحديث حالة طلب الالتحاق الخاص بـ {$name} إلى: {$statusLabel}.",
            'application_id' => $this->application->id,
        ];
    }
}
