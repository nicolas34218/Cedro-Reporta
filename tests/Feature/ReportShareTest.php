<?php

use App\Models\Category;
use App\Models\Report;
use App\Models\ReportShare;
use App\Models\Secretary;
use Illuminate\Support\Facades\Notification;

describe('Report Sharing Between Secretaries', function () {
    beforeEach(function () {
        Notification::fake();

        Category::create([
            'name' => 'Infraestrutura',
            'description' => 'Categoria de teste',
            'is_active' => true,
        ]);

        $this->origin = Secretary::create([
            'name' => 'Secretaria de Origem',
            'email' => 'origem@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->destination = Secretary::create([
            'name' => 'Secretaria Compartilhada',
            'email' => 'compartilhada@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->report = Report::create([
            'user_id' => null,
            'title' => 'Buraco na rua principal',
            'description' => 'Existe um buraco grande na via principal.',
            'category' => 'Infraestrutura',
            'status' => 'Pendente',
            'location' => 'Rua A - Centro',
            'location_address' => 'Rua A - Centro',
            'latitude' => null,
            'longitude' => null,
            'image_path' => null,
            'secretary_id' => $this->origin->id,
            'is_anonymous' => false,
        ]);
    });

    it('allows the origin secretary to share a report with another secretary', function () {
        $response = $this->actingAs($this->origin, 'secretary')
            ->post(route('secretary.share.store', $this->report), [
                'to_secretary_id' => $this->destination->id,
                'message' => 'A ocorrência também envolve a sua área.',
            ]);

        $response->assertRedirect(route('secretary.reports.show', $this->report));

        $this->assertDatabaseHas('report_shares', [
            'report_id' => $this->report->id,
            'from_secretary_id' => $this->origin->id,
            'to_secretary_id' => $this->destination->id,
        ]);

        expect(ReportShare::count())->toBe(1);
        Notification::assertSentTo($this->destination, \App\Notifications\ReportSharedWithSecretary::class);
    });

    it('keeps the report under the origin secretary and exposes it through share relations', function () {
        $response = $this->actingAs($this->origin, 'secretary')
            ->post(route('secretary.share.store', $this->report), [
                'to_secretary_id' => $this->destination->id,
                'message' => 'A ocorrência também envolve a sua área.',
            ]);

        $response->assertRedirect();

        $share = ReportShare::first();

        expect($this->report->refresh()->secretary_id)->toBe($this->origin->id);
        expect($share->report_id)->toBe($this->report->id);
        expect($share->to_secretary_id)->toBe($this->destination->id);
        expect($this->destination->receivedShares()->count())->toBe(1);
        expect($this->destination->sharedReports()->whereKey($this->report->id)->exists())->toBeTrue();
    });
});