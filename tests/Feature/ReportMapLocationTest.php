<?php

use App\Models\Category;
use App\Models\Report;
use Illuminate\Support\Facades\Http;

describe('Report Map Location', function () {
    it('resolves coordinates into a readable address', function () {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([
                'display_name' => 'Rua das Flores, Centro, Cedro - CE, Brasil',
            ], 200),
        ]);

        $response = $this->getJson(route('reports.location.resolve', [
            'latitude' => -6.6050000,
            'longitude' => -39.0620000,
        ]));

        $response->assertOk();
        $response->assertJson([
            'latitude' => -6.605,
            'longitude' => -39.062,
            'address' => 'Rua das Flores, Centro, Cedro - CE, Brasil',
        ]);
    });

    it('stores a visitor report using map coordinates and the resolved address', function () {
        Category::create([
            'name' => 'Infraestrutura',
            'description' => 'Categoria de teste',
            'is_active' => true,
        ]);

        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([
                'display_name' => 'Avenida Principal, Centro, Cedro - CE, Brasil',
            ], 200),
        ]);

        $response = $this->from(route('visitor.reports.create'))
            ->withSession(['report_captcha_answer' => 9])
            ->post(route('visitor.reports.store'), [
                'title' => 'Buraco na via',
                'description' => 'Existe um buraco grande na via principal, próximo à praça.',
                'category' => 'Infraestrutura',
                'latitude' => -6.6050000,
                'longitude' => -39.0620000,
                'captcha_answer' => '9',
            ]);

        expect($response->status())->toBe(302);
        expect($response->headers->get('Location'))->toBe(route('visitor.reports.create'));

        $report = Report::first();
        expect($report)->not->toBeNull();
        expect($report->latitude)->toBe(-6.6050000);
        expect($report->longitude)->toBe(-39.0620000);
        expect($report->location_address)->toBe('Avenida Principal, Centro, Cedro - CE, Brasil');
        expect($report->location)->toBe('Avenida Principal, Centro, Cedro - CE, Brasil');
        expect($report->user_id)->toBeNull();
    });
});
