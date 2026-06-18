<?php

use App\Enums\TransferStatus;
use App\Models\Report;
use App\Models\ReportTransfer;
use App\Models\Secretary;

describe('Report Transfer Between Secretaries', function () {
    beforeEach(function () {
        $this->origin = Secretary::create([
            'name' => 'Secretaria de Buracos',
            'email' => 'buracos@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->destination = Secretary::create([
            'name' => 'Secretaria de Iluminação',
            'email' => 'iluminacao@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->report = Report::factory()->create([
            'secretary_id' => $this->origin->id,
        ]);
    });

    it('requires a justification to transfer a report', function () {
        $response = $this->actingAs($this->origin, 'secretary')
            ->post(route('secretary.transfer.store', $this->report), [
                'to_secretary_id' => $this->destination->id,
            ]);

        $response->assertSessionHasErrors('justification');
        expect(ReportTransfer::count())->toBe(0);
    });

    it('requires a destination secretary different from the current one', function () {
        $response = $this->actingAs($this->origin, 'secretary')
            ->post(route('secretary.transfer.store', $this->report), [
                'to_secretary_id' => $this->origin->id,
                'justification' => 'Motivo qualquer com mais de dez caracteres.',
            ]);

        $response->assertSessionHasErrors('to_secretary_id');
        expect(ReportTransfer::count())->toBe(0);
    });

    it('creates a pending transfer, keeps the report with the origin and notifies the destination', function () {
        $response = $this->actingAs($this->origin, 'secretary')
            ->post(route('secretary.transfer.store', $this->report), [
                'to_secretary_id' => $this->destination->id,
                'justification' => 'Não compete a esta secretaria, é um problema de iluminação.',
            ]);

        $response->assertRedirect(route('secretary.transfer.index'));

        $this->assertDatabaseHas('report_transfers', [
            'report_id' => $this->report->id,
            'from_secretary_id' => $this->origin->id,
            'to_secretary_id' => $this->destination->id,
            'status' => TransferStatus::PENDING,
        ]);

        expect($this->report->refresh()->secretary_id)->toBe($this->origin->id);
        expect($this->destination->notifications()->count())->toBe(1);
    });

    it('prevents transferring a report that already has a pending transfer', function () {
        ReportTransfer::create([
            'report_id' => $this->report->id,
            'from_secretary_id' => $this->origin->id,
            'to_secretary_id' => $this->destination->id,
            'justification' => 'Primeira transferência.',
            'status' => TransferStatus::PENDING,
        ]);

        $response = $this->actingAs($this->origin, 'secretary')
            ->post(route('secretary.transfer.store', $this->report), [
                'to_secretary_id' => $this->destination->id,
                'justification' => 'Segunda tentativa de transferência.',
            ]);

        $response->assertSessionHas('error');
        expect(ReportTransfer::count())->toBe(1);
    });

    it('moves the report to the destination secretary when the transfer is accepted', function () {
        $transfer = ReportTransfer::create([
            'report_id' => $this->report->id,
            'from_secretary_id' => $this->origin->id,
            'to_secretary_id' => $this->destination->id,
            'justification' => 'Não compete a esta secretaria.',
            'status' => TransferStatus::PENDING,
        ]);

        $response = $this->actingAs($this->destination, 'secretary')
            ->post(route('secretary.transfer.accept', $transfer));

        $response->assertRedirect(route('secretary.transfer.index'));

        expect($this->report->refresh()->secretary_id)->toBe($this->destination->id);
        expect($transfer->refresh()->status)->toBe(TransferStatus::ACCEPTED);
        expect($transfer->decided_at)->not->toBeNull();
    });

    it('requires a rejection reason and keeps the report with the origin secretary', function () {
        $transfer = ReportTransfer::create([
            'report_id' => $this->report->id,
            'from_secretary_id' => $this->origin->id,
            'to_secretary_id' => $this->destination->id,
            'justification' => 'Não compete a esta secretaria.',
            'status' => TransferStatus::PENDING,
        ]);

        $response = $this->actingAs($this->destination, 'secretary')
            ->post(route('secretary.transfer.reject', $transfer));

        $response->assertSessionHasErrors('rejection_reason');

        $response = $this->actingAs($this->destination, 'secretary')
            ->post(route('secretary.transfer.reject', $transfer), [
                'rejection_reason' => 'Também não é da nossa competência.',
            ]);

        $response->assertRedirect(route('secretary.transfer.index'));

        expect($this->report->refresh()->secretary_id)->toBe($this->origin->id);
        expect($transfer->refresh()->status)->toBe(TransferStatus::REJECTED);
        expect($transfer->rejection_reason)->toBe('Também não é da nossa competência.');
    });

    it('prevents a secretary other than the destination from deciding the transfer', function () {
        $transfer = ReportTransfer::create([
            'report_id' => $this->report->id,
            'from_secretary_id' => $this->origin->id,
            'to_secretary_id' => $this->destination->id,
            'justification' => 'Não compete a esta secretaria.',
            'status' => TransferStatus::PENDING,
        ]);

        $response = $this->actingAs($this->origin, 'secretary')
            ->post(route('secretary.transfer.accept', $transfer));

        $response->assertStatus(403);
        expect($transfer->refresh()->status)->toBe(TransferStatus::PENDING);
    });
});
