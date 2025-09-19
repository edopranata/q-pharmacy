<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Obat Bebas',
            'Obat Keras',
            'Obat Bebas Terbatas',
            'Suplemen',
            'Alat Kesehatan',
            'Kosmetik',
            'Herbal',
            'Vitamin',
            'Antibiotik',
            'Analgesik',
            'Antiseptik',
            'Antasida',
            'Antihistamin',
            'Dekongestan',
            'Ekspektoran',
            'Laksatif',
            'Probiotik',
            'Imunomodulator',
            'Kontrasepsi',
            'Diabetes',
            'Hipertensi',
            'Jantung',
            'Ginjal',
            'Liver',
            'Mata',
            'Telinga',
            'Kulit',
            'Rambut',
            'Gigi',
            'Mulut',
            'Tenggorokan',
            'Pernapasan',
            'Pencernaan',
            'Saraf',
            'Tulang',
            'Sendi',
            'Otot',
            'Darah',
            'Hormon',
            'Reproduksi',
            'Anak',
            'Lansia',
            'Ibu Hamil',
            'Menyusui',
            'Perawatan Luka',
            'Bedah',
            'Anestesi',
            'Radiologi',
            'Laboratorium',
        ];

        // Use random element without unique constraint to avoid overflow
        $name = $this->faker->randomElement($categories);
        $code = 'CAT'.str_pad($this->faker->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT);

        return [
            'name' => $name,
            'code' => $code,
            'description' => $this->faker->sentence(10),
            'is_active' => $this->faker->boolean(85), // 85% chance to be active
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the category is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the category is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
