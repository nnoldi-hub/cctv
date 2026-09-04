<?php

namespace Database\Seeders;

use App\Models\Equipment;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['name' => 'Camera exterior 2MP', 'category' => 'camera', 'sku' => 'CAM-EXT-2MP', 'unit' => 'buc', 'unit_price' => 180, 'stock_quantity' => 40],
            ['name' => 'Camera exterior 4MP', 'category' => 'camera', 'sku' => 'CAM-EXT-4MP', 'unit' => 'buc', 'unit_price' => 260, 'stock_quantity' => 30],
            ['name' => 'Camera interior 2MP', 'category' => 'camera', 'sku' => 'CAM-INT-2MP', 'unit' => 'buc', 'unit_price' => 150, 'stock_quantity' => 30],
            ['name' => 'NVR 4 canale', 'category' => 'nvr', 'sku' => 'NVR-4CH', 'unit' => 'buc', 'unit_price' => 450, 'stock_quantity' => 15],
            ['name' => 'NVR 8 canale', 'category' => 'nvr', 'sku' => 'NVR-8CH', 'unit' => 'buc', 'unit_price' => 650, 'stock_quantity' => 15],
            ['name' => 'NVR 16 canale', 'category' => 'nvr', 'sku' => 'NVR-16CH', 'unit' => 'buc', 'unit_price' => 1100, 'stock_quantity' => 10],
            ['name' => 'HDD supraveghere 1TB', 'category' => 'accessory', 'sku' => 'HDD-1TB', 'unit' => 'buc', 'unit_price' => 220, 'stock_quantity' => 25],
            ['name' => 'HDD supraveghere 2TB', 'category' => 'accessory', 'sku' => 'HDD-2TB', 'unit' => 'buc', 'unit_price' => 320, 'stock_quantity' => 25],
            ['name' => 'Cablu UTP cat.6', 'category' => 'cable', 'sku' => 'CBL-UTP6', 'unit' => 'metru', 'unit_price' => 3.5, 'stock_quantity' => 2000],
            ['name' => 'Sursa alimentare 12V', 'category' => 'accessory', 'sku' => 'PSU-12V', 'unit' => 'buc', 'unit_price' => 45, 'stock_quantity' => 60],
            ['name' => 'Conector RJ45 + mufa', 'category' => 'accessory', 'sku' => 'CON-RJ45', 'unit' => 'buc', 'unit_price' => 2, 'stock_quantity' => 500],
            ['name' => 'Manopera instalare / camera', 'category' => 'other', 'sku' => 'LABOR-CAM', 'unit' => 'buc', 'unit_price' => 80, 'stock_quantity' => 0],
        ];

        foreach ($items as $item) {
            Equipment::updateOrCreate(['sku' => $item['sku']], $item);
        }
    }
}
