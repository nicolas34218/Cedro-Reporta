<?php

use App\Models\Category;
use App\Models\Citizen;
use App\Models\Report;
use App\Models\Secretary;

describe('Anonymous Report Submission', function () {
    beforeEach(function () {
        $this->citizen = Citizen::factory()->create(['name' => 'Maria da Silva']);

        $this->category = Category::create([
            'name' => 'Infraestrutura',
            'description' => 'Categoria de teste',
            'is_active' => true,
        ]);
    });

    it('shows the anonymous checkbox to authenticated citizens', function () {
        $response = $this->actingAs($this->citizen, 'citizen')
            ->get(route('citizen.reports.create'));

        $response->assertStatus(200);
        $response->assertSee('Manter denúncia anônima');
    });

    it('does not show the anonymous checkbox to visitors', function () {
        $response = $this->get(route('visitor.reports.create'));

        $response->assertStatus(200);
        $response->assertDontSee('Manter denúncia anônima');
    });

    it('creates an anonymous report when the citizen checks the box', function () {
        $response = $this->actingAs($this->citizen, 'citizen')
            ->withSession(['report_captcha_answer' => 4])
            ->post(route('citizen.reports.store'), [
                'title' => 'Vazamento de esgoto na rua',
                'description' => 'Vazamento de esgoto há dias sem solução por parte da prefeitura',
                'category' => $this->category->name,
                'address_reference' => 'Rua das Flores, 100',
                'district' => 'Centro',
                'captcha_answer' => '4',
                'anonymous' => '1',
            ]);

        $response->assertRedirect(route('citizen.reports.index'));

        $report = Report::first();
        expect($report)->not->toBeNull();
        expect($report->user_id)->toBe($this->citizen->id);
        expect($report->is_anonymous)->toBeTrue();
    });

    it('creates a non-anonymous report when the citizen leaves the box unchecked', function () {
        $response = $this->actingAs($this->citizen, 'citizen')
            ->withSession(['report_captcha_answer' => 4])
            ->post(route('citizen.reports.store'), [
                'title' => 'Buraco na rua principal',
                'description' => 'Buraco grande causando transtornos no trânsito local',
                'category' => $this->category->name,
                'address_reference' => 'Rua das Flores, 100',
                'district' => 'Centro',
                'captcha_answer' => '4',
            ]);

        $response->assertRedirect(route('citizen.reports.index'));

        $report = Report::first();
        expect($report->is_anonymous)->toBeFalse();
    });

    it('ignores the anonymous flag on visitor report submissions', function () {
        $response = $this->withSession(['report_captcha_answer' => 9])
            ->post(route('visitor.reports.store'), [
                'title' => 'Lixo acumulado na esquina',
                'description' => 'Lixo acumulado gerando mau odor na vizinhança',
                'category' => $this->category->name,
                'address_reference' => 'Avenida Central, 50',
                'district' => 'Centro',
                'captcha_answer' => '9',
                'anonymous' => '1',
            ]);

        $report = Report::first();
        expect($report)->not->toBeNull();
        expect($report->user_id)->toBeNull();
        expect($report->is_anonymous)->toBeFalse();
    });

    it('hides the citizen identity from the secretary dashboard when the report is anonymous', function () {
        $secretary = Secretary::create([
            'name' => 'Secretaria de Infraestrutura',
            'email' => 'infraestrutura@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $report = Report::factory()->create([
            'user_id' => $this->citizen->id,
            'secretary_id' => $secretary->id,
            'is_anonymous' => true,
        ])->load('citizen');

        $html = (string) $this->actingAs($secretary, 'secretary')
            ->view('secretary.dashboard', [
                'reports' => collect([$report]),
                'statistics' => ['total_reports' => 1, 'pending_reports' => 0, 'analyzing_reports' => 0, 'resolved_reports' => 0],
                'category' => $secretary->name,
                'categories' => collect(),
            ]);

        expect($html)->toContain('Anônimo');
        expect($html)->not->toContain($this->citizen->name);
    });

    it('shows the citizen name to the secretary when the report is not anonymous', function () {
        $secretary = Secretary::create([
            'name' => 'Secretaria de Infraestrutura',
            'email' => 'infraestrutura2@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $report = Report::factory()->create([
            'user_id' => $this->citizen->id,
            'secretary_id' => $secretary->id,
            'is_anonymous' => false,
        ])->load('citizen');

        $html = (string) $this->actingAs($secretary, 'secretary')
            ->view('secretary.dashboard', [
                'reports' => collect([$report]),
                'statistics' => ['total_reports' => 1, 'pending_reports' => 0, 'analyzing_reports' => 0, 'resolved_reports' => 0],
                'category' => $secretary->name,
                'categories' => collect(),
            ]);

        expect($html)->toContain($this->citizen->name);
    });
});
