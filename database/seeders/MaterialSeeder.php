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
     * Seeder ini bersifat idempoten (menggunakan updateOrCreate) dan hanya memuat
     * bahan yang telah memiliki nama, kategori, satuan acuan, dan harga normalisasi
     * yang tercatat secara pasti pada dokumen.
     */
    public function run(): void
    {
        $materials = [
            [
                'name' => 'Daging Sapi',
                'category' => 'daging',
                'selling_mode' => 'curah',
                'unit' => 'Kg',
                'package_unit' => 'Kg',
                'package_weight' => 1000.00,
                'package_price' => 140000.00,
                'price' => 140.00, // Rp140.000 / kg -> Rp140 / gram
                'is_available' => true,
            ],
            [
                'name' => 'Daging Ayam',
                'category' => 'daging',
                'selling_mode' => 'curah',
                'unit' => 'Kg',
                'package_unit' => 'Kg',
                'package_weight' => 1000.00,
                'package_price' => 40000.00,
                'price' => 40.00, // Rp40.000 / kg -> Rp40 / gram
                'is_available' => true,
            ],
            [
                'name' => 'Tepung Tapioka',
                'category' => 'tepung',
                'selling_mode' => 'kemasan',
                'unit' => 'Pack',
                'package_unit' => 'Pack',
                'package_weight' => 500.00,
                'package_price' => 9000.00,
                'price' => 18.00, // Rp9.000 / 500 gram -> Rp18 / gram
                'is_available' => true,
            ],
            [
                'name' => 'Tepung ProBakso',
                'category' => 'tepung',
                'selling_mode' => 'kemasan',
                'unit' => 'Pack',
                'package_unit' => 'Pack',
                'package_weight' => 250.00,
                'package_price' => 10000.00,
                'price' => 40.00, // Rp10.000 / 250 gram -> Rp40 / gram
                'is_available' => true,
            ],
            [
                'name' => 'Tepung Terigu',
                'category' => 'tepung',
                'selling_mode' => 'kemasan',
                'unit' => 'Pack',
                'package_unit' => 'Pack',
                'package_weight' => 500.00,
                'package_price' => 6000.00,
                'price' => 12.00, // Rp6.000 / 500 gram -> Rp12 / gram
                'is_available' => true,
            ],
            [
                'name' => 'Tepung Sagu',
                'category' => 'tepung',
                'selling_mode' => 'kemasan',
                'unit' => 'Pack',
                'package_unit' => 'Pack',
                'package_weight' => 250.00,
                'package_price' => 6000.00,
                'price' => 24.00, // Rp6.000 / 250 gram -> Rp24 / gram
                'is_available' => true,
            ],
            [
                'name' => 'Bawang Putih Kupas',
                'category' => 'bumbu',
                'selling_mode' => 'curah',
                'unit' => 'Pack',
                'package_unit' => 'Pack',
                'package_weight' => 100.00,
                'package_price' => 4000.00,
                'price' => 40.00, // Rp40.000 / kg (eceran katalog 100g Rp4.000) -> Rp40 / gram
                'is_available' => true,
            ],
            [
                'name' => 'Bawang Merah Goreng',
                'category' => 'bumbu',
                'selling_mode' => 'kemasan',
                'unit' => 'Pack',
                'package_unit' => 'Pack',
                'package_weight' => 100.00,
                'package_price' => 8000.00,
                'price' => 80.00, // Rp8.000 / 100 gram -> Rp80 / gram
                'is_available' => true,
            ],
            [
                'name' => 'Garam Kasar',
                'category' => 'bumbu',
                'selling_mode' => 'curah',
                'unit' => 'Pack',
                'package_unit' => 'Pack',
                'package_weight' => 250.00,
                'package_price' => 1500.00,
                'price' => 6.00, // Rp1.500 / 250 gram (Rp6.000/kg eceran) -> Rp6 / gram
                'is_available' => true,
            ],
            [
                'name' => 'Telur Ayam',
                'category' => 'pengikat',
                'selling_mode' => 'satuan',
                'unit' => 'Butir',
                'package_unit' => 'Butir',
                'package_weight' => 60.00,
                'package_price' => 2500.00,
                'price' => 2500.00, // Rp2.500 / butir (~60g)
                'is_available' => true,
            ],
            [
                'name' => 'Merica Bubuk Sachet',
                'category' => 'bumbu',
                'selling_mode' => 'satuan',
                'unit' => 'Sachet',
                'package_unit' => 'Sachet',
                'package_weight' => 4.00,
                'package_price' => 1000.00,
                'price' => 1000.00, // Rp1.000 / sachet (Ladaku ~4g)
                'is_available' => true,
            ],
            [
                'name' => 'Pengenyal Sobaso',
                'category' => 'tambahan',
                'selling_mode' => 'satuan',
                'unit' => 'Sachet',
                'package_unit' => 'Sachet',
                'package_weight' => 32.00,
                'package_price' => 4500.00,
                'price' => 4500.00, // Rp4.500 / sachet asli 32g
                'is_available' => true,
            ],
            [
                'name' => 'Penyedap Masako',
                'category' => 'penyedap',
                'selling_mode' => 'satuan',
                'unit' => 'Sachet',
                'package_unit' => 'Sachet',
                'package_weight' => 9.00,
                'package_price' => 500.00,
                'price' => 500.00, // Rp500 / sachet 9g
                'is_available' => true,
            ],
        ];

        // Migrasi data nama Garam Halus kembali ke Garam Kasar jika ada
        Material::where('name', 'Garam Halus')->update([
            'name' => 'Garam Kasar',
            'selling_mode' => 'curah',
            'unit' => 'Pack',
            'package_unit' => 'Pack',
            'package_weight' => 250.00,
            'package_price' => 1500.00,
            'price' => 6.00,
        ]);

        foreach ($materials as $item) {
            Material::updateOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
