<?php

use App\Enums\ReportStatus;
use App\Models\Admin;
use App\Models\Secretary;
use App\Models\Citizen;
use App\Models\Report;

describe('Admin report status update', function () {
    beforeEach(function () {
        $this->adminUser = Admin::factory()->create();

        $this->secretaryUser = Secretary::factory()->create();

        $this->citizenUser = Citizen::factory()->create();

        $this->report = Report::factory()->create([
            'status' => 'Aberta',
        ]);
    });

    it('allows admin to update report status', function () {
        $response = $this->actingAs($this->adminUser, 'admin')
            ->put(route('admin.report.status', $this->report), [
                'status' => 'Em Análise',
            ]);

        $response->assertRedirect(route('admin.report.show', $this->report));
        $this->assertDatabaseHas('reports', [
            'id' => $this->report->id,
            'status' => 'Em Análise',
        ]);
    });

    it('prevents citizen from updating report status', function () {
        $response = $this->actingAs($this->citizenUser, 'citizen')
            ->put(route('admin.report.status', $this->report), [
                'status' => 'Resolvida',
            ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('reports', [
            'id' => $this->report->id,
            'status' => 'Aberta',
        ]);
    });

    it('does not accept invalid status values', function () {
        $response = $this->actingAs($this->adminUser, 'admin')
            ->from(route('admin.report.show', $this->report))
            ->put(route('admin.report.status', $this->report), [
                'status' => 'Status Inválido',
            ]);

        $response->assertRedirect(route('admin.report.show', $this->report));
        $response->assertSessionHasErrors(['status']);
        $this->assertDatabaseHas('reports', [
            'id' => $this->report->id,
            'status' => 'Aberta',
        ]);
    });
});
