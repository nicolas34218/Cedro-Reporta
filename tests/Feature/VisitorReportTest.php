<?php

use App\Models\Report;
use App\Models\Category;

describe('Visitor Report Submission', function () {
    it('shows the visitor report creation page without authentication', function () {
        $response = $this->get(route('visitor.reports.create'));

        $response->assertStatus(200);
        $response->assertSee('Registrar Denúncia como Visitante');
        $response->assertSee('não poderá receber notificações automáticas');
    });

    it('creates a visitor report when captcha is correct', function () {
        $category = Category::create([
            'name' => 'Infraestrutura',
            'description' => 'Categoria de teste',
            'is_active' => true,
        ]);

        $response = $this->withSession(['report_captcha_answer' => 5])
            ->post(route('visitor.reports.store'), [
                'title' => 'Poste apagado',
                'description' => 'O poste está apagado há vários dias no bairro.',
                'category' => $category->name,
                'address_reference' => 'Rua A, em frente ao mercado',
                'district' => 'Centro',
                'captcha_answer' => '5',
            ]);

        $response->assertRedirect(route('visitor.reports.create'));
        $response->assertSessionHas('success');
        $response->assertSessionHas('visitor_notice');

        $report = Report::first();
        expect($report)->not->toBeNull();
        expect($report->title)->toBe('Poste apagado');
        expect($report->user_id)->toBeNull();
    });

    it('rejects visitor report submission with invalid captcha', function () {
        $category = Category::create([
            'name' => 'Infraestrutura',
            'description' => 'Categoria de teste',
            'is_active' => true,
        ]);

        $response = $this->withSession(['report_captcha_answer' => 7])
            ->post(route('visitor.reports.store'), [
                'title' => 'Ponto de lixo',
                'description' => 'Há lixo acumulado no local há dias.',
                'category' => $category->name,
                'address_reference' => 'Av. Principal, esquina',
                'district' => 'Centro',
                'captcha_answer' => '3',
            ]);

        $response->assertSessionHasErrors('captcha_answer');
        $response->assertStatus(302);
        expect(Report::count())->toBe(0);
    });
});
