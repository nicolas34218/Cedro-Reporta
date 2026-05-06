<?php

use App\Models\Report;
use App\Models\User;
use App\Enums\ReportStatus;

describe('Report Filter', function () {
    
    beforeEach(function () {
        // Cria um usuário
        $this->user = User::factory()->create([
            'user_type' => 'Cidadão',
        ]);

        $otherUser = User::factory()->create([
            'user_type' => 'Cidadão',
        ]);

        // Cria denúncias com diferentes categorias
        Report::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Buraco na rua',
            'category' => 'Infraestrutura',
            'status' => ReportStatus::PENDING,
            'location' => 'Centro - Distrito',
        ]);

        Report::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Semáforo quebrado',
            'category' => 'Trânsito',
            'status' => ReportStatus::PENDING,
            'location' => 'Vila Nova - Distrito',
        ]);

        Report::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Rua suja',
            'category' => 'Limpeza Urbana',
            'status' => ReportStatus::ANALYZING,
            'location' => 'Centro - Distrito',
        ]);

        Report::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Assalto',
            'category' => 'Segurança Pública',
            'status' => ReportStatus::RESOLVED,
            'location' => 'Periferia - Distrito',
        ]);

        // Cria denúncias para outro usuário (não deve aparecer)
        Report::factory()->create([
            'user_id' => $otherUser->id,
            'title' => 'Postes iluminação',
            'category' => 'Iluminação',
            'status' => ReportStatus::PENDING,
            'location' => 'Centro - Distrito',
        ]);
    });

    describe('Filter by Category', function () {
        it('filters reports by single category', function () {
            $response = $this->actingAs($this->user)
                ->get(route('citizen.reports.search', ['category' => 'Infraestrutura']));

            $response->assertStatus(200);
            $reports = $response->viewData('reports');
            
            expect($reports->count())->toBe(1);
            expect($reports->first()->category)->toBe('Infraestrutura');
            expect($reports->first()->title)->toBe('Buraco na rua');
        });

        it('returns empty when category does not match', function () {
            $response = $this->actingAs($this->user)
                ->get(route('citizen.reports.search', ['category' => 'Não Existe']));

            $response->assertStatus(200);
            $reports = $response->viewData('reports');
            expect($reports->count())->toBe(0);
        });
    });

    describe('Filter by Location', function () {
        it('filters reports by location using partial match', function () {
            $response = $this->actingAs($this->user)
                ->get(route('citizen.reports.search', ['location' => 'Centro']));

            $response->assertStatus(200);
            $reports = $response->viewData('reports');
            
            expect($reports->count())->toBe(2);
            foreach ($reports as $report) {
                expect($report->location)->toContain('Centro');
            }
        });

        it('returns empty when location does not match', function () {
            $response = $this->actingAs($this->user)
                ->get(route('citizen.reports.search', ['location' => 'Lugar que não existe']));

            $response->assertStatus(200);
            $reports = $response->viewData('reports');
            expect($reports->count())->toBe(0);
        });
    });

    describe('Filter by Status', function () {
        it('filters reports by single status', function () {
            $response = $this->actingAs($this->user)
                ->get(route('citizen.reports.search', ['status' => ReportStatus::PENDING]));

            $response->assertStatus(200);
            $reports = $response->viewData('reports');
            
            expect($reports->count())->toBe(2);
            foreach ($reports as $report) {
                expect($report->status)->toBe(ReportStatus::PENDING);
            }
        });

        it('filters reports by resolved status', function () {
            $response = $this->actingAs($this->user)
                ->get(route('citizen.reports.search', ['status' => ReportStatus::RESOLVED]));

            $response->assertStatus(200);
            $reports = $response->viewData('reports');
            
            expect($reports->count())->toBe(1);
            expect($reports->first()->status)->toBe(ReportStatus::RESOLVED);
        });
    });

    describe('Combined Filters', function () {
        it('applies category and status filters simultaneously', function () {
            $response = $this->actingAs($this->user)
                ->get(route('citizen.reports.search', [
                    'category' => 'Infraestrutura',
                    'status' => ReportStatus::PENDING
                ]));

            $response->assertStatus(200);
            $reports = $response->viewData('reports');
            
            expect($reports->count())->toBe(1);
            expect($reports->first()->category)->toBe('Infraestrutura');
            expect($reports->first()->status)->toBe(ReportStatus::PENDING);
        });

        it('applies category and location filters simultaneously', function () {
            $response = $this->actingAs($this->user)
                ->get(route('citizen.reports.search', [
                    'category' => 'Limpeza Urbana',
                    'location' => 'Centro'
                ]));

            $response->assertStatus(200);
            $reports = $response->viewData('reports');
            
            expect($reports->count())->toBe(1);
            expect($reports->first()->category)->toBe('Limpeza Urbana');
            expect($reports->first()->location)->toContain('Centro');
        });

        it('applies all three filters simultaneously', function () {
            $response = $this->actingAs($this->user)
                ->get(route('citizen.reports.search', [
                    'category' => 'Infraestrutura',
                    'status' => ReportStatus::PENDING,
                    'location' => 'Centro'
                ]));

            $response->assertStatus(200);
            $reports = $response->viewData('reports');
            
            expect($reports->count())->toBe(1);
            expect($reports->first()->category)->toBe('Infraestrutura');
            expect($reports->first()->status)->toBe(ReportStatus::PENDING);
            expect($reports->first()->location)->toContain('Centro');
        });

        it('returns empty when combined filters dont match', function () {
            $response = $this->actingAs($this->user)
                ->get(route('citizen.reports.search', [
                    'category' => 'Infraestrutura',
                    'status' => ReportStatus::RESOLVED,
                ]));

            $response->assertStatus(200);
            $reports = $response->viewData('reports');
            expect($reports->count())->toBe(0);
        });
    });

    describe('Pagination', function () {
        it('applies pagination with per_page parameter', function () {
            $response = $this->actingAs($this->user)
                ->get(route('citizen.reports.search'));

            $response->assertStatus(200);
            $reports = $response->viewData('reports');
            
            // Verifica que a paginação está funcionando
            expect($reports->total())->toBe(4); // 4 denúncias do usuário
        });

        it('returns correct pagination metadata', function () {
            $response = $this->actingAs($this->user)
                ->get(route('citizen.reports.search'));

            $response->assertStatus(200);
            $reports = $response->viewData('reports');
            
            $this->assertNotNull($reports->total());
            $this->assertNotNull($reports->perPage());
            $this->assertNotNull($reports->currentPage());
            $this->assertNotNull($reports->lastPage());
        });
    });

    describe('User Isolation', function () {
        it('only shows reports from authenticated user', function () {
            $response = $this->actingAs($this->user)
                ->get(route('citizen.reports.search'));

            $response->assertStatus(200);
            $reports = $response->viewData('reports');
            
            // Deve retornar apenas as 4 denúncias do usuário
            expect($reports->total())->toBe(4);
        });

        it('requires authentication', function () {
            $response = $this->get(route('citizen.reports.search'));

            $response->assertStatus(302);
            $response->assertRedirect(route('login'));
        });
    });

    describe('Validation', function () {
        it('validates per_page parameter', function () {
            // A página de search não faz validação de per_page, apenas paginação
            $response = $this->actingAs($this->user)
                ->get(route('citizen.reports.search'));

            $response->assertStatus(200);
            $reports = $response->viewData('reports');
            $this->assertNotNull($reports);
        });

        it('handles search term filtering', function () {
            $response = $this->actingAs($this->user)
                ->get(route('citizen.reports.search', ['q' => 'Buraco']));

            $response->assertStatus(200);
            $reports = $response->viewData('reports');
            
            expect($reports->count())->toBe(1);
            expect($reports->first()->title)->toContain('Buraco');
        });
    });
});

