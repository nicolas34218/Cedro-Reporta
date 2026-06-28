<?php

use App\Models\Category;
use App\Models\Citizen;
use App\Models\Report;
use App\Models\ReportHistory;
use App\Models\ReportTransfer;
use App\Models\Secretary;

describe('Report History', function () {
    beforeEach(function () {
        $this->secretary = Secretary::create([
            'name' => 'Secretaria de Infraestrutura',
            'email' => 'infraestrutura@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->otherSecretary = Secretary::create([
            'name' => 'Secretaria de Iluminação',
            'email' => 'iluminacao@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->citizen = Citizen::factory()->create(['name' => 'Maria da Silva']);

        Category::create([
            'name' => 'Infraestrutura',
            'description' => 'Categoria de teste',
            'is_active' => true,
            'secretary_id' => $this->secretary->id,
        ]);

        $this->report = Report::factory()->create([
            'user_id' => $this->citizen->id,
            'secretary_id' => $this->secretary->id,
            'status' => 'Pendente',
        ]);
    });

    it('records a history entry when a citizen creates a report', function () {
        $response = $this->actingAs($this->citizen, 'citizen')
            ->withSession(['report_captcha_answer' => 4])
            ->post(route('citizen.reports.store'), [
                'title' => 'Buraco na rua principal',
                'description' => 'Buraco grande causando transtornos no trânsito local',
                'category' => 'Infraestrutura',
                'address_reference' => 'Rua das Flores, 100',
                'district' => 'Centro',
                'captcha_answer' => '4',
            ]);

        $response->assertRedirect(route('citizen.reports.index'));

        $report = Report::where('title', 'Buraco na rua principal')->first();
        expect($report)->not->toBeNull();
        expect($report->histories)->toHaveCount(1);

        $entry = $report->histories->first();
        expect($entry->action)->toBe('Denúncia registrada');
        expect($entry->actor_name)->toBe('Maria da Silva');
        expect($entry->actor_role)->toBe('Cidadão');
    });

    it('records a history entry with the secretary as actor when the status is updated', function () {
        $response = $this->actingAs($this->secretary, 'secretary')
            ->putJson(route('secretary.report.status', $this->report), [
                'status' => 'Em Análise',
            ]);

        $response->assertOk();

        $entry = $this->report->histories()->latest()->first();
        expect($entry->action)->toBe('Status atualizado');
        expect($entry->actor_name)->toBe($this->secretary->name);
        expect($entry->actor_role)->toBe('Secretaria');
        expect($entry->description)->toContain('Pendente')->toContain('Em Análise');
    });

    it('records a history entry when the priority is classified', function () {
        $this->actingAs($this->secretary, 'secretary')
            ->put(route('priority.update', $this->report), [
                'priority' => 'Alta',
            ]);

        $entry = $this->report->histories()->latest()->first();
        expect($entry->action)->toBe('Prioridade definida');
        expect($entry->actor_role)->toBe('Secretaria');
        expect($entry->description)->toContain('Alta');
    });

    it('records history entries through the full transfer lifecycle', function () {
        $this->actingAs($this->secretary, 'secretary')
            ->post(route('secretary.transfer.store', $this->report), [
                'to_secretary_id' => $this->otherSecretary->id,
                'justification' => 'Não compete a esta secretaria, é da iluminação.',
            ]);

        expect($this->report->histories()->where('action', 'Transferência solicitada')->count())->toBe(1);

        $transfer = ReportTransfer::where('report_id', $this->report->id)->first();

        $this->actingAs($this->otherSecretary, 'secretary')
            ->post(route('secretary.transfer.accept', $transfer));

        expect($this->report->histories()->where('action', 'Transferência aceita')->count())->toBe(1);
    });

    it('records a history entry when a report is shared with another secretary', function () {
        $response = $this->actingAs($this->secretary, 'secretary')
            ->post(route('secretary.share.store', $this->report), [
                'to_secretary_id' => $this->otherSecretary->id,
                'message' => 'Favor avaliar também sob a ótica da iluminação pública.',
            ]);

        $response->assertRedirect(route('secretary.reports.show', $this->report));

        $entry = $this->report->histories()->latest()->first();
        expect($entry->action)->toBe('Compartilhada com outra secretaria');
        expect($entry->description)->toContain($this->otherSecretary->name);
    });

    it('allows the citizen owner to see the full history on the track-status page', function () {
        ReportHistory::log($this->report, 'Status atualizado', 'Status alterado de "Pendente" para "Em Análise".');

        $response = $this->actingAs($this->citizen, 'citizen')
            ->get(route('citizen.reports.track-status', $this->report));

        $response->assertOk();
        $response->assertSee('Status atualizado');
    });

    it('prevents a citizen from seeing another citizen report history', function () {
        $otherCitizen = Citizen::factory()->create();

        $response = $this->actingAs($otherCitizen, 'citizen')
            ->get(route('citizen.reports.track-status', $this->report));

        $response->assertStatus(403);
    });

    it('allows the responsible secretary to see the report history page', function () {
        ReportHistory::log($this->report, 'Status atualizado', 'Status alterado de "Pendente" para "Em Análise".');

        $response = $this->actingAs($this->secretary, 'secretary')
            ->get(route('secretary.reports.show', $this->report));

        $response->assertOk();
        $response->assertSee('Status atualizado');
    });

    it('prevents a secretary that is not responsible from seeing the report history page', function () {
        $response = $this->actingAs($this->otherSecretary, 'secretary')
            ->get(route('secretary.reports.show', $this->report));

        $response->assertStatus(403);
    });

    it('allows a secretary with whom the report was shared to see the history page', function () {
        \App\Models\ReportShare::create([
            'report_id' => $this->report->id,
            'from_secretary_id' => $this->secretary->id,
            'to_secretary_id' => $this->otherSecretary->id,
            'message' => null,
            'shared_at' => now(),
        ]);

        $response = $this->actingAs($this->otherSecretary, 'secretary')
            ->get(route('secretary.reports.show', $this->report));

        $response->assertOk();
    });
});
