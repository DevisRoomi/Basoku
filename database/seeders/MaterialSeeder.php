<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Data bersumber resmi dari:
     * - Dokumen 3: DOKUMENTASI DATA HARGA BAHAN BAKSO DAN PEMETAAN KATALOG BASOKU
     * - Dokumen 4: DOKUMENTASI FORMULA ACUAN, LOGIKA PERHITUNGAN BIAYA, DAN IMPLEMENTASI KALKULATOR BASOKU
     *
     * Seeder ini bersifat idempoten (berbasis slug kanonik) agar perubahan nama di panel admin tidak memicu duplikasi.
     */
    public function run(): void
    {
        $materials = [
            [
                'slug' => 'daging-sapi',
                'name' => 'Daging Sapi',
                'category' => 'daging',
                'selling_mode' => 'curah',
                'unit' => 'Kg',
                'package_unit' => 'Kg',
                'package_weight' => 1000.00,
                'package_price' => 140000.00,
                'price' => 140.00,
                'is_available' => true,
            ],
            [
                'slug' => 'daging-ayam',
                'name' => 'Daging Ayam',
                'category' => 'daging',
                'selling_mode' => 'curah',
                'unit' => 'Kg',
                'package_unit' => 'Kg',
                'package_weight' => 1000.00,
                'package_price' => 40000.00,
                'price' => 40.00,
                'is_available' => true,
            ],
            [
                'slug' => 'tepung-tapioka',
                'name' => 'Tepung Tapioka',
                'category' => 'tepung',
                'selling_mode' => 'kemasan',
                'unit' => 'Pack',
                'package_unit' => 'Pack',
                'package_weight' => 500.00,
                'package_price' => 9000.00,
                'price' => 18.00,
                'is_available' => true,
            ],
            [
                'slug' => 'tepung-probakso',
                'name' => 'Tepung ProBakso',
                'category' => 'tepung',
                'selling_mode' => 'kemasan',
                'unit' => 'Pack',
                'package_unit' => 'Pack',
                'package_weight' => 250.00,
                'package_price' => 10000.00,
                'price' => 40.00,
                'is_available' => true,
            ],
            [
                'slug' => 'tepung-terigu',
                'name' => 'Tepung Terigu',
                'category' => 'tepung',
                'selling_mode' => 'kemasan',
                'unit' => 'Pack',
                'package_unit' => 'Pack',
                'package_weight' => 500.00,
                'package_price' => 6000.00,
                'price' => 12.00,
                'is_available' => true,
            ],
            [
                'slug' => 'tepung-sagu',
                'name' => 'Tepung Sagu',
                'category' => 'tepung',
                'selling_mode' => 'kemasan',
                'unit' => 'Pack',
                'package_unit' => 'Pack',
                'package_weight' => 250.00,
                'package_price' => 6000.00,
                'price' => 24.00,
                'is_available' => true,
            ],
            [
                'slug' => 'bawang-putih-kupas',
                'name' => 'Bawang Putih Kupas',
                'category' => 'bumbu',
                'selling_mode' => 'curah',
                'unit' => 'Pack',
                'package_unit' => 'Pack',
                'package_weight' => 100.00,
                'package_price' => 4000.00,
                'price' => 40.00,
                'is_available' => true,
            ],
            [
                'slug' => 'bawang-merah-goreng',
                'name' => 'Bawang Merah Goreng',
                'category' => 'bumbu',
                'selling_mode' => 'kemasan',
                'unit' => 'Pack',
                'package_unit' => 'Pack',
                'package_weight' => 100.00,
                'package_price' => 8000.00,
                'price' => 80.00,
                'is_available' => true,
            ],
            [
                'slug' => 'garam-kasar',
                'name' => 'Garam Kasar',
                'category' => 'bumbu',
                'selling_mode' => 'curah',
                'unit' => 'Pack',
                'package_unit' => 'Pack',
                'package_weight' => 250.00,
                'package_price' => 1500.00,
                'price' => 6.00,
                'is_available' => true,
            ],
            [
                'slug' => 'telur-ayam',
                'name' => 'Telur Ayam',
                'category' => 'pengikat',
                'selling_mode' => 'satuan',
                'unit' => 'Butir',
                'package_unit' => 'Butir',
                'package_weight' => 60.00,
                'package_price' => 2500.00,
                'price' => 2500.00,
                'is_available' => true,
            ],
            [
                'slug' => 'merica-bubuk-sachet',
                'name' => 'Merica Bubuk Sachet',
                'category' => 'bumbu',
                'selling_mode' => 'satuan',
                'unit' => 'Sachet',
                'package_unit' => 'Sachet',
                'package_weight' => 4.00,
                'package_price' => 1000.00,
                'price' => 1000.00,
                'is_available' => true,
            ],
            [
                'slug' => 'pengenyal-sobaso',
                'name' => 'Pengenyal Sobaso',
                'category' => 'tambahan',
                'selling_mode' => 'satuan',
                'unit' => 'Sachet',
                'package_unit' => 'Sachet',
                'package_weight' => 32.00,
                'package_price' => 4500.00,
                'price' => 4500.00,
                'is_available' => true,
            ],
            [
                'slug' => 'penyedap-masako',
                'name' => 'Penyedap Masako',
                'category' => 'penyedap',
                'selling_mode' => 'satuan',
                'unit' => 'Sachet',
                'package_unit' => 'Sachet',
                'package_weight' => 9.00,
                'package_price' => 500.00,
                'price' => 500.00,
                'is_available' => true,
            ],
        ];

        // Migrasi data lama Garam Halus ke Garam Kasar jika ada
        Material::where('name', 'Garam Halus')->update([
            'slug' => 'garam-kasar',
            'name' => 'Garam Kasar',
            'selling_mode' => 'curah',
            'unit' => 'Pack',
            'package_unit' => 'Pack',
            'package_weight' => 250.00,
            'package_price' => 1500.00,
            'price' => 6.00,
        ]);

        foreach ($materials as $item) {
            $existing = Material::where('slug', $item['slug'])
                ->orWhere('name', $item['name'])
                ->first();

            if ($existing) {
                $existing->update($item);
            } else {
                Material::create($item);
            }
        }
    }
}
