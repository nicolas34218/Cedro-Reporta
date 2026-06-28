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

    it('lets the origin secretary see the share form on the sharing screen', function () {
        $response = $this->actingAs($this->origin, 'secretary')
            ->get(route('secretary.share.create', $this->report));

        $response->assertOk();
        $response->assertSee('Compartilhar com');
    });

    it('lets a secretary with whom the report was shared open the sharing screen without the share form', function () {
        ReportShare::create([
            'report_id' => $this->report->id,
            'from_secretary_id' => $this->origin->id,
            'to_secretary_id' => $this->destination->id,
            'message' => null,
            'shared_at' => now(),
        ]);

        $response = $this->actingAs($this->destination, 'secretary')
            ->get(route('secretary.share.create', $this->report));

        $response->assertOk();
        $response->assertDontSee('Compartilhar com');
        $response->assertSee('Atualizações sobre a Denúncia');
    });

    it('blocks an unrelated secretary from opening the sharing screen', function () {
        $unrelated = Secretary::create([
            'name' => 'Secretaria Sem Relação',
            'email' => 'sem-relacao@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $response = $this->actingAs($unrelated, 'secretary')
            ->get(route('secretary.share.create', $this->report));

        $response->assertStatus(403);
    });

    it('lets the origin secretary post a progress update on the sharing screen', function () {
        $response = $this->actingAs($this->origin, 'secretary')
            ->post(route('secretary.reports.updates.store', $this->report), [
                'content' => 'Equipe técnica já foi enviada ao local para avaliação.',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $entry = $this->report->histories()->latest()->first();
        expect($entry->action)->toBe('Atualização sobre a denúncia');
        expect($entry->description)->toBe('Equipe técnica já foi enviada ao local para avaliação.');
        expect($entry->actor_name)->toBe($this->origin->name);
        expect($entry->actor_role)->toBe('Secretaria');
    });

    it('lets a secretary with whom the report was shared post a progress update', function () {
        ReportShare::create([
            'report_id' => $this->report->id,
            'from_secretary_id' => $this->origin->id,
            'to_secretary_id' => $this->destination->id,
            'message' => null,
            'shared_at' => now(),
        ]);

        $response = $this->actingAs($this->destination, 'secretary')
            ->post(route('secretary.reports.updates.store', $this->report), [
                'content' => 'Nossa equipe também está acompanhando a ocorrência.',
            ]);

        $response->assertRedirect();

        $entry = $this->report->histories()->latest()->first();
        expect($entry->actor_name)->toBe($this->destination->name);
    });

    it('blocks an unrelated secretary from posting a progress update', function () {
        $unrelated = Secretary::create([
            'name' => 'Secretaria Sem Relação',
            'email' => 'sem-relacao-2@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $response = $this->actingAs($unrelated, 'secretary')
            ->post(route('secretary.reports.updates.store', $this->report), [
                'content' => 'Tentativa de atualização indevida.',
            ]);

        $response->assertStatus(403);
    });

    it('requires a minimum length for the progress update content', function () {
        $response = $this->actingAs($this->origin, 'secretary')
            ->post(route('secretary.reports.updates.store', $this->report), [
                'content' => 'Oi',
            ]);

        $response->assertSessionHasErrors('content');
        expect($this->report->histories()->count())->toBe(0);
    });
});