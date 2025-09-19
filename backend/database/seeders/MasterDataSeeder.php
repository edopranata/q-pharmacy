<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Configuration for data amounts
        $categoryCount = env('SEED_CATEGORY_COUNT', 10);
        $supplierCount = env('SEED_SUPPLIER_COUNT', 8);
        $unitCount = env('SEED_UNIT_COUNT', 10);

        // Seed Categories using factory
        Category::factory($categoryCount)->create();

        // Ensure we have some active categories
        Category::factory(max(1, intval($categoryCount * 0.8)))->active()->create();

        // Seed Units using factory
        Unit::factory($unitCount)->create();

        // Ensure we have some active units
        Unit::factory(max(1, intval($unitCount * 0.9)))->active()->create();

        // Seed Suppliers using factory
        Supplier::factory($supplierCount)->create();

        // Ensure we have some active suppliers
        Supplier::factory(max(1, intval($supplierCount * 0.8)))->active()->create();

        // Create some essential master data with fixed values for consistency
        $this->createEssentialData();
    }

    /**
     * Create essential master data with fixed values
     */
    private function createEssentialData(): void
    {
        // Essential Categories
        $essentialCategories = [
            ['name' => 'Obat Bebas', 'code' => 'OTC', 'description' => 'Obat yang dapat dibeli tanpa resep dokter', 'is_active' => true],
            ['name' => 'Obat Keras', 'code' => 'ETH', 'description' => 'Obat yang memerlukan resep dokter', 'is_active' => true],
            ['name' => 'Suplemen', 'code' => 'SUP', 'description' => 'Suplemen makanan dan vitamin', 'is_active' => true],
        ];

        foreach ($essentialCategories as $category) {
            Category::firstOrCreate(
                ['code' => $category['code']],
                $category
            );
        }

        // Essential Units
        $essentialUnits = [
            ['name' => 'Tablet', 'code' => 'TAB', 'symbol' => 'tab', 'is_active' => true],
            ['name' => 'Kapsul', 'code' => 'CAP', 'symbol' => 'cap', 'is_active' => true],
            ['name' => 'Botol', 'code' => 'BTL', 'symbol' => 'btl', 'is_active' => true],
            ['name' => 'Box', 'code' => 'BOX', 'symbol' => 'box', 'is_active' => true],
        ];

        foreach ($essentialUnits as $unit) {
            Unit::firstOrCreate(
                ['code' => $unit['code']],
                $unit
            );
        }

        // Essential Suppliers
        $essentialSuppliers = [
            [
                'name' => 'PT Kimia Farma',
                'code' => 'KF001',
                'contact_person' => 'Budi Santoso',
                'phone' => '021-12345678',
                'email' => 'sales@kimiafarma.co.id',
                'address' => 'Jl. Veteran No. 9, Jakarta Pusat',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'postal_code' => '10110',
                'description' => 'Distributor obat-obatan terpercaya',
                'is_active' => true,
            ],
            [
                'name' => 'PT Kalbe Farma',
                'code' => 'KB001',
                'contact_person' => 'Siti Nurhaliza',
                'phone' => '021-87654321',
                'email' => 'order@kalbe.co.id',
                'address' => 'Jl. Letjen Suprapto, Cempaka Putih',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'postal_code' => '10510',
                'description' => 'Produsen farmasi terkemuka',
                'is_active' => true,
            ],
        ];

        foreach ($essentialSuppliers as $supplier) {
            Supplier::firstOrCreate(
                ['code' => $supplier['code']],
                $supplier
            );
        }
    }
}
