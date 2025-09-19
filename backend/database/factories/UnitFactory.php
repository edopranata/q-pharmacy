<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Unit>
 */
class UnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $units = [
            ['name' => 'Tablet', 'symbol' => 'tab'],
            ['name' => 'Kapsul', 'symbol' => 'kaps'],
            ['name' => 'Botol', 'symbol' => 'btl'],
            ['name' => 'Tube', 'symbol' => 'tube'],
            ['name' => 'Sachet', 'symbol' => 'scht'],
            ['name' => 'Ampul', 'symbol' => 'amp'],
            ['name' => 'Vial', 'symbol' => 'vial'],
            ['name' => 'Strip', 'symbol' => 'strip'],
            ['name' => 'Box', 'symbol' => 'box'],
            ['name' => 'Pieces', 'symbol' => 'pcs'],
            ['name' => 'Lembar', 'symbol' => 'lbr'],
            ['name' => 'Meter', 'symbol' => 'm'],
            ['name' => 'Liter', 'symbol' => 'l'],
            ['name' => 'Mililiter', 'symbol' => 'ml'],
            ['name' => 'Gram', 'symbol' => 'g'],
            ['name' => 'Kilogram', 'symbol' => 'kg'],
            ['name' => 'Set', 'symbol' => 'set'],
            ['name' => 'Pack', 'symbol' => 'pack'],
            ['name' => 'Roll', 'symbol' => 'roll'],
            ['name' => 'Dus', 'symbol' => 'dus'],
        ];

        $unit = $this->faker->randomElement($units);
        $code = 'UNIT'.str_pad($this->faker->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT);

        return [
            'name' => $unit['name'],
            'code' => $code,
            'symbol' => $unit['symbol'],
            'description' => $this->faker->sentence(8),
            'is_active' => $this->faker->boolean(95), // 95% chance to be active
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the unit is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the unit is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
