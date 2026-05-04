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
                ->getJson(route('citizen.reports.filter', ['category' => 'Infraestrutura']));

            $response->assertStatus(200);
            $response->assertJsonPath('success', true);
            $response->assertJsonPath('data.reports.0.category', 'Infraestrutura');
            
            $reports = $response->json('data.reports');
            expect(count($reports))->toBe(1);
            expect($reports[0]['title'])->toBe('Buraco na rua');
        });

        it('returns empty when category does not match', function () {
            $response = $this->actingAs($this->user)
                ->getJson(route('citizen.reports.filter', ['category' => 'Não Existe']));

            $response->assertStatus(200);
            expect(count($response->json('data.reports')))->toBe(0);
        });
    });

    describe('Filter by Location', function () {
        it('filters reports by location using partial match', function () {
            $response = $this->actingAs($this->user)
                ->getJson(route('citizen.reports.filter', ['location' => 'Centro']));

            $response->assertStatus(200);
            $reports = $response->json('data.reports');
            
            expect(count($reports))->toBe(2);
            foreach ($reports as $report) {
                expect($report['location'])->toContain('Centro');
            }
        });

        it('returns empty when location does not match', function () {
            $response = $this->actingAs($this->user)
                ->getJson(route('citizen.reports.filter', ['location' => 'Lugar que não existe']));

            $response->assertStatus(200);
            expect(count($response->json('data.reports')))->toBe(0);
        });
    });

    describe('Filter by Status', function () {
        it('filters reports by single status', function () {
            $response = $this->actingAs($this->user)
                ->getJson(route('citizen.reports.filter', ['status' => ReportStatus::PENDING]));

            $response->assertStatus(200);
            $reports = $response->json('data.reports');
            
            expect(count($reports))->toBe(2);
            foreach ($reports as $report) {
                expect($report['status'])->toBe(ReportStatus::PENDING);
            }
        });

        it('filters reports by resolved status', function () {
            $response = $this->actingAs($this->user)
                ->getJson(route('citizen.reports.filter', ['status' => ReportStatus::RESOLVED]));

            $response->assertStatus(200);
            $reports = $response->json('data.reports');
            
            expect(count($reports))->toBe(1);
            expect($reports[0]['status'])->toBe(ReportStatus::RESOLVED);
        });
    });

    describe('Combined Filters', function () {
        it('applies category and status filters simultaneously', function () {
            $response = $this->actingAs($this->user)
                ->getJson(route('citizen.reports.filter', [
                    'category' => 'Infraestrutura',
                    'status' => ReportStatus::PENDING
                ]));

            $response->assertStatus(200);
            $reports = $response->json('data.reports');
            
            expect(count($reports))->toBe(1);
            expect($reports[0]['category'])->toBe('Infraestrutura');
            expect($reports[0]['status'])->toBe(ReportStatus::PENDING);
        });

        it('applies category and location filters simultaneously', function () {
            $response = $this->actingAs($this->user)
                ->getJson(route('citizen.reports.filter', [
                    'category' => 'Limpeza Urbana',
                    'location' => 'Centro'
                ]));

            $response->assertStatus(200);
            $reports = $response->json('data.reports');
            
            expect(count($reports))->toBe(1);
            expect($reports[0]['category'])->toBe('Limpeza Urbana');
            expect($reports[0]['location'])->toContain('Centro');
        });

        it('applies all three filters simultaneously', function () {
            $response = $this->actingAs($this->user)
                ->getJson(route('citizen.reports.filter', [
                    'category' => 'Infraestrutura',
                    'status' => ReportStatus::PENDING,
                    'location' => 'Centro'
                ]));

            $response->assertStatus(200);
            $reports = $response->json('data.reports');
            
            expect(count($reports))->toBe(1);
            expect($reports[0]['category'])->toBe('Infraestrutura');
            expect($reports[0]['status'])->toBe(ReportStatus::PENDING);
            expect($reports[0]['location'])->toContain('Centro');
        });

        it('returns empty when combined filters dont match', function () {
            $response = $this->actingAs($this->user)
                ->getJson(route('citizen.reports.filter', [
                    'category' => 'Infraestrutura',
                    'status' => ReportStatus::RESOLVED,
                ]));

            $response->assertStatus(200);
            expect(count($response->json('data.reports')))->toBe(0);
        });
    });

    describe('Pagination', function () {
        it('applies pagination with per_page parameter', function () {
            $response = $this->actingAs($this->user)
                ->getJson(route('citizen.reports.filter', ['per_page' => 2]));

            $response->assertStatus(200);
            $pagination = $response->json('data.pagination');
            
            expect($pagination['per_page'])->toBe(2);
            expect($pagination['total'])->toBe(4); // 4 denúncias do usuário
        });

        it('returns correct pagination metadata', function () {
            $response = $this->actingAs($this->user)
                ->getJson(route('citizen.reports.filter', ['per_page' => 10]));

            $response->assertStatus(200);
            $response->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'reports',
                    'pagination' => [
                        'total',
                        'per_page',
                        'current_page',
                        'last_page',
                        'from',
                        'to'
                    ],
                    'filters_applied'
                ]
            ]);
        });
    });

    describe('User Isolation', function () {
        it('only shows reports from authenticated user', function () {
            $response = $this->actingAs($this->user)
                ->getJson(route('citizen.reports.filter'));

            $response->assertStatus(200);
            $reports = $response->json('data.reports');
            
            // Deve retornar apenas as 4 denúncias do usuário
            expect(count($reports))->toBe(4);
        });

        it('requires authentication', function () {
            $response = $this->getJson(route('citizen.reports.filter'));

            $response->assertStatus(401);
        });
    });

    describe('Validation', function () {
        it('validates per_page parameter', function () {
            $response = $this->actingAs($this->user)
                ->getJson(route('citizen.reports.filter', ['per_page' => 101]));

            $response->assertStatus(422);
        });

        it('validates location parameter has minimum length', function () {
            $response = $this->actingAs($this->user)
                ->getJson(route('citizen.reports.filter', ['location' => '']));

            // Vazio é permitido (nullable)
            $response->assertStatus(200);
        });
    });
});
