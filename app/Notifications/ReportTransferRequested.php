<?php

namespace App\Notifications;

use App\Models\ReportTransfer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportTransferRequested extends Notification
{
    use Queueable;

    /**
     * @var ReportTransfer
     */
    protected $transfer;

    public $type = 'report_transfer_requested';

    public function __construct(ReportTransfer $transfer)
    {
        $this->transfer = $transfer;
    }

    /**
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        $report = $this->transfer->report;

        return [
            'type' => $this->type,
            'transfer_id' => $this->transfer->id,
            'report_id' => $report->id,
            'report_title' => $report->title,
            'from_secretary_name' => $this->transfer->fromSecretary->name,
            'justification' => $this->transfer->justification,
            'message' => "Nova denúncia encaminhada por {$this->transfer->fromSecretary->name}: {$report->title}",
            'url' => route('secretary.transfer.index'),
            'created_at' => now(),
        ];
    }

    /**
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        $report = $this->transfer->report;

        return (new MailMessage)
            ->subject('Nova denúncia encaminhada para sua secretaria')
            ->line("A {$this->transfer->fromSecretary->name} encaminhou uma denúncia para sua secretaria: {$report->title}")
            ->line("Justificativa: {$this->transfer->justification}")
            ->action('Ver encaminhamentos', route('secretary.transfer.index'))
            ->line('Obrigado por usar Cedro Reporta!');
    }
}
