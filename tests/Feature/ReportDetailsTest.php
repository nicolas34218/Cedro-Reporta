<?php

use App\Models\Report;
use App\Models\User;
use App\Enums\ReportStatus;

describe('Report Details', function () {
    
    beforeEach(function () {
        // Cria um usuário para testes
        $this->user = User::factory()->create([
            'user_type' => 'Cidadão',
        ]);

        // Cria um relatório para o usuário
        $this->report = Report::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Denúncia de Teste',
            'description' => 'Esta é uma denúncia para teste de visualização de detalhes',
            'category' => 'Infraestrutura',
            'status' => ReportStatus::PENDING,
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
        $otherUser = User::factory()->create([
            'user_type' => 'Cidadão',
        ]);

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

    it('api endpoint returns report details in json', function () {
        $response = $this->actingAs($this->user)
            ->getJson(route('citizen.reports.details', $this->report));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'report' => [
                    'id',
                    'title',
                    'description',
                    'category',
                    'status',
                    'status_config',
                    'location',
                    'created_at',
                    'created_at_human',
                    'updated_at',
                ],
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
                'full_data' => [
                    'id',
                    'image_path',
                    'created_at_iso',
                    'updated_at_iso',
                    'timestamp_created',
                ],
            ],
        ]);
    });

    it('api endpoint includes user information', function () {
        $response = $this->actingAs($this->user)
            ->getJson(route('citizen.reports.details', $this->report));

        $response->assertJsonPath('data.user.id', $this->user->id);
        $response->assertJsonPath('data.user.name', $this->user->name);
        $response->assertJsonPath('data.user.email', $this->user->email);
    });

    it('api endpoint includes status config with colors and icons', function () {
        $response = $this->actingAs($this->user)
            ->getJson(route('citizen.reports.details', $this->report));

        $statusConfig = $response->json('data.report.status_config');
        
        $this->assertArrayHasKey('color', $statusConfig);
        $this->assertArrayHasKey('icon', $statusConfig);
        $this->assertArrayHasKey('description', $statusConfig);
    });

    it('api endpoint cannot be accessed by other users', function () {
        $otherUser = User::factory()->create([
            'user_type' => 'Cidadão',
        ]);

        $response = $this->actingAs($otherUser)
            ->getJson(route('citizen.reports.details', $this->report));

        $response->assertStatus(403);
    });
});
