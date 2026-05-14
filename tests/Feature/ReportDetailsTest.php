<?php

use App\Models\Report;
use App\Models\Citizen;

describe('Report Details', function () {
    
    beforeEach(function () {
        // Cria um cidadão para testes
        $this->user = Citizen::factory()->create();

        // Cria um relatório para o cidadão
        $this->report = Report::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Denúncia de Teste',
            'description' => 'Esta é uma denúncia para teste de visualização de detalhes',
            'category' => 'Infraestrutura',
            'status' => 'Aberta',
            'location' => 'Centro - Distrito',
            'image_path' => null,
        ]);
    });

    it('user can view their own report details', function () {
        $response = $this->actingAs($this->user)
            ->get(route('citizen.reports.show', $this->report));

        $response->assertStatus(200);
        $response->assertViewHas('report');
        $response->assertViewHas('reportFormatted');
    });

    it('user cannot view another user report details', function () {
        $otherUser = Citizen::factory()->create();

        $response = $this->actingAs($otherUser)
            ->get(route('citizen.reports.show', $this->report));

        $response->assertStatus(403);
    });

    it('unauthenticated user cannot view report details', function () {
        $response = $this->get(route('citizen.reports.show', $this->report));

        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    });

    it('returns formatted report data with dates', function () {
        $response = $this->actingAs($this->user)
            ->get(route('citizen.reports.show', $this->report));

        $reportFormatted = $response->viewData('reportFormatted');

        $this->assertArrayHasKey('id', $reportFormatted);
        $this->assertArrayHasKey('title', $reportFormatted);
        $this->assertArrayHasKey('description', $reportFormatted);
        $this->assertArrayHasKey('category', $reportFormatted);
        $this->assertArrayHasKey('status', $reportFormatted);
        $this->assertArrayHasKey('location', $reportFormatted);
        $this->assertArrayHasKey('created_at', $reportFormatted);
        $this->assertArrayHasKey('status_config', $reportFormatted);
        
        // Verifica formato de data
        $this->assertMatchesRegularExpression('/^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}$/', $reportFormatted['created_at']);
    });
});

