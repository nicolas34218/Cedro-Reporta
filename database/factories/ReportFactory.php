<?php

namespace Database\Factories;

use App\Enums\ReportStatus;
use App\Models\Report;
use App\Models\Citizen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => Citizen::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(3),
            'category' => fake()->randomElement([
                'Infraestrutura',
                'Trânsito',
                'Limpeza Urbana',
                'Segurança Pública',
                'Saúde',
                'Educação',
                'Iluminação',
                'Outro'
            ]),
            'status' => ReportStatus::PENDING,
            'location' => fake()->address(),
            'image_path' => null,
        ];
    }

    /**
     * Indicate that the report should be in analysis state.
     */
    public function analyzing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReportStatus::ANALYZING,
        ]);
    }

    /**
     * Indicate that the report should be resolved.
     */
    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReportStatus::RESOLVED,
        ]);
    }

    /**
     * Indicate that the report should be closed.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReportStatus::CLOSED,
        ]);
    }

    /**
     * Indicate that the report should have an image.
     */
    public function withImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'image_path' => 'reports/' . fake()->sha1() . '.jpg',
        ]);
    }
}
