<?php

namespace App\Notifications;

use App\Models\ReportShare;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReportSharedWithSecretary extends Notification
{
    use Queueable;

    public function __construct(protected ReportShare $share)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $report = $this->share->report;

        return [
            'type' => 'report_shared',
            'share_id' => $this->share->id,
            'report_id' => $report->id,
            'report_title' => $report->title,
            'from_secretary_name' => $this->share->fromSecretary->name,
            'message' => "A denúncia #{$report->id} foi compartilhada com sua secretaria.",
            'url' => route('secretary.reports.show', $report),
            'created_at' => now(),
        ];
    }
}