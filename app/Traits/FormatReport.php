<?php

namespace App\Traits;

use App\Enums\ReportStatus;

/**
 * Trait para formatação consistente de denúncias.
 * 
 * Evita duplicação de código ao formatar dados de denúncias
 * em diferentes contextos (views, APIs, etc).
 */
trait FormatReport
{
    /**
     * Formata uma denúncia para exibição consistente.
     * 
     * @param mixed $report
     * @param bool $includeDescription
     * @return array
     */
    protected function formatReport($report, bool $includeDescription = true): array
    {
        $data = [
            'id' => $report->id,
            'title' => $report->title,
            'category' => $report->category,
            'status' => $report->status,
            'status_config' => ReportStatus::getStatusConfig($report->status),
            'location' => $report->location,
            'location_address' => $report->location_address,
            'latitude' => $report->latitude,
            'longitude' => $report->longitude,
            'created_at' => $report->created_at->format('d/m/Y H:i'),
            'created_at_human' => $report->created_at->diffForHumans(),
            'updated_at' => $report->updated_at->format('d/m/Y H:i'),
        ];

        if ($includeDescription) {
            $data['description'] = $report->description;
        }

        return $data;
    }
}
