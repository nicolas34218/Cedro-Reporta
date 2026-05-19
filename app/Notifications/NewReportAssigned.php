<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReportAssigned extends Notification
{
    use Queueable;

    /**
     * A denúncia que foi atribuída.
     *
     * @var Report
     */
    protected $report;

    /**
     * O tipo de notificação.
     *
     * @var string
     */
    public $type = 'new_report_assigned';

    /**
     * Criar uma nova notificação.
     *
     * @param Report $report
     */
    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    /**
     * Obter os canais que a notificação deve ser entregue.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // Usar 'database' para armazenar no banco de dados
        return ['database'];
    }

    /**
     * Obter a representação da notificação para armazenamento no banco de dados.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'type' => $this->type,
            'report_id' => $this->report->id,
            'report_title' => $this->report->title,
            'report_category' => $this->report->category,
            'citizen_name' => $this->report->citizen->name ?? 'Cidadão',
            'message' => "Nova denúncia atribuída: {$this->report->title}",
            'url' => route('secretary.classify-reports'),
            'created_at' => now(),
        ];
    }

    /**
     * Obter a representação mail da notificação.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        $citizenName = $this->report->citizen->name ?? 'Não informado';

        return (new MailMessage)
            ->subject('Nova Denúncia Atribuída')
            ->line("Uma nova denúncia foi atribuída a você: {$this->report->title}")
            ->line("Categoria: {$this->report->category}")
            ->line("Cidadão: {$citizenName}")
            ->action('Ver Denúncia', route('secretary.classify-reports'))
            ->line('Obrigado por usar Cedro Reporta!');
    }
}