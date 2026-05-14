<?php

use App\Enums\ReportStatus;
use App\Models\Report;
use App\Models\User;

describe('Admin report status update', function () {
    beforeEach(function () {
        $this->adminUser = User::factory()->create([
            'user_type' => 'Admin',
        ]);

        $this->secretaryUser = User::factory()->create([
            'user_type' => 'Secretário',
        ]);

        $this->citizenUser = User::factory()->create([
            'user_type' => 'Cidadão',
        ]);

        $this->report = Report::factory()->create([
            'status' => ReportStatus::PENDING,
        ]);
    });

    it('allows admin to update report status', function () {
        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.report.status', $this->report), [
                'status' => ReportStatus::ANALYZING,
            ]);

        $response->assertRedirect(route('admin.report.show', $this->report));
        $this->assertDatabaseHas('reports', [
            'id' => $this->report->id,
            'status' => ReportStatus::ANALYZING,
        ]);
    });

    it('allows secretary to update report status', function () {
        $response = $this->actingAs($this->secretaryUser)
            ->put(route('admin.report.status', $this->report), [
                'status' => ReportStatus::RESOLVED,
            ]);

        $response->assertRedirect(route('admin.report.show', $this->report));
        $this->assertDatabaseHas('reports', [
            'id' => $this->report->id,
            'status' => ReportStatus::RESOLVED,
        ]);
    });

    it('prevents citizen from updating report status', function () {
        $response = $this->actingAs($this->citizenUser)
            ->put(route('admin.report.status', $this->report), [
                'status' => ReportStatus::RESOLVED,
            ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('reports', [
            'id' => $this->report->id,
            'status' => ReportStatus::PENDING,
        ]);
    });

    it('does not accept invalid status values', function () {
        $response = $this->actingAs($this->adminUser)
            ->from(route('admin.report.show', $this->report))
            ->put(route('admin.report.status', $this->report), [
                'status' => 'Status Inválido',
            ]);

        $response->assertRedirect(route('admin.report.show', $this->report));
        $response->assertSessionHasErrors(['status']);
        $this->assertDatabaseHas('reports', [
            'id' => $this->report->id,
            'status' => ReportStatus::PENDING,
        ]);
    });
});
