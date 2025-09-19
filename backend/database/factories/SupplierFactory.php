<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $companies = [
            'PT Kimia Farma',
            'PT Kalbe Farma',
            'PT Dexa Medica',
            'PT Sanbe Farma',
            'PT Indofarma',
            'PT Pharos Indonesia',
            'PT Bernofarm',
            'PT Combiphar',
            'PT Tempo Scan Pacific',
            'PT Mahakam Beta Farma',
            'PT Novell Pharmaceutical',
            'PT Hexpharm Jaya',
            'PT Soho Industri Pharmasi',
            'PT Merck Indonesia',
            'PT Bayer Indonesia',
            'PT Pfizer Indonesia',
            'PT Novartis Indonesia',
            'PT Sanofi Indonesia',
            'PT Abbott Indonesia',
            'PT Roche Indonesia',
            'CV Apotek Sehat',
            'CV Medika Utama',
            'CV Pharma Jaya',
            'CV Obat Murah',
            'CV Kesehatan Prima',
            'PT Distributor Medis',
            'PT Supplier Farmasi',
            'PT Grosir Obat',
            'PT Toko Kesehatan',
            'PT Apotek Bersama',
        ];

        // Generate company name with some variation
        $baseName = $this->faker->randomElement($companies);
        $name = $baseName.' '.$this->faker->optional(0.3)->randomElement(['Cabang', 'Regional', 'Pusat', 'Tbk']);
        $code = 'SUP'.str_pad($this->faker->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT);

        return [
            'name' => $name,
            'code' => $code,
            'contact_person' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'province' => $this->faker->state(),
            'postal_code' => $this->faker->postcode(),
            'description' => $this->faker->sentence(15),
            'is_active' => $this->faker->boolean(90), // 90% chance to be active
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the supplier is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the supplier is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
