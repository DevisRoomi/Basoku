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
                'unit' => 'Kg',
                'package_unit' => 'Kg',
                'package_weight' => 1000.00,
                'package_price' => 120000.00,
                'price' => 120.00, // Rp120.000 / kg -> Rp120 / gram
                'is_available' => true,
            ],
            [
                'name' => 'Daging Ayam',
                'category' => 'daging',
                'unit' => 'Kg',
                'package_unit' => 'Kg',
                'package_weight' => 1000.00,
                'package_price' => 45000.00,
                'price' => 45.00, // Rp45.000 / kg -> Rp45 / gram
                'is_available' => true,
            ],
            [
                'name' => 'Tepung Tapioka',
                'category' => 'tepung',
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
                'unit' => 'Kg',
                'package_unit' => 'Kg',
                'package_weight' => 1000.00,
                'package_price' => 12000.00,
                'price' => 12.00, // Rp12.000 / kg -> Rp12 / gram
                'is_available' => true,
            ],
            [
                'name' => 'Tepung Sagu',
                'category' => 'tepung',
                'unit' => 'Pack',
                'package_unit' => 'Pack',
                'package_weight' => 500.00,
                'package_price' => 10000.00,
                'price' => 20.00, // Rp10.000 / 500 gram -> Rp20 / gram
                'is_available' => true,
            ],
            [
                'name' => 'Bawang Putih Kupas',
                'category' => 'bumbu',
                'unit' => 'Pack',
                'package_unit' => 'Pack',
                'package_weight' => 100.00,
                'package_price' => 4000.00,
                'price' => 40.00, // Rp4.000 / 100 gram -> Rp40 / gram
                'is_available' => true,
            ],
            [
                'name' => 'Bawang Merah Goreng',
                'category' => 'bumbu',
                'unit' => 'Pack',
                'package_unit' => 'Pack',
                'package_weight' => 100.00,
                'package_price' => 8000.00,
                'price' => 80.00, // Rp8.000 / 100 gram -> Rp80 / gram
                'is_available' => true,
            ],
            [
                'name' => 'Garam Halus',
                'category' => 'bumbu',
                'unit' => 'Pack',
                'package_unit' => 'Pack',
                'package_weight' => 250.00,
                'package_price' => 3500.00,
                'price' => 14.00, // Rp3.500 / 250g -> Rp14 / gram
                'is_available' => true,
            ],
            [
                'name' => 'Telur Ayam',
                'category' => 'pengikat',
                'unit' => 'Butir',
                'package_unit' => 'Butir',
                'package_weight' => 60.00,
                'package_price' => 2500.00,
                'price' => 2500.00, // Rp2.500 / butir
                'is_available' => true,
            ],
            [
                'name' => 'Merica Bubuk Sachet',
                'category' => 'bumbu',
                'unit' => 'Sachet',
                'package_unit' => 'Sachet',
                'package_weight' => 5.00,
                'package_price' => 1000.00,
                'price' => 1000.00, // Rp1.000 / sachet 5g
                'is_available' => true,
            ],
            [
                'name' => 'Pengenyal Sobaso',
                'category' => 'tambahan',
                'unit' => 'Sachet',
                'package_unit' => 'Sachet',
                'package_weight' => 32.00,
                'package_price' => 4500.00,
                'price' => 4500.00, // Rp4.500 / sachet 32g
                'is_available' => true,
            ],
            [
                'name' => 'Penyedap Masako',
                'category' => 'penyedap',
                'unit' => 'Sachet',
                'package_unit' => 'Sachet',
                'package_weight' => 10.00,
                'package_price' => 1000.00,
                'price' => 1000.00, // Rp1.000 / sachet 10g
                'is_available' => true,
            ],
        ];

        // Migrasi data nama lama jika ada
        Material::where('name', 'Garam Kasar')->update([
            'name' => 'Garam Halus',
            'package_weight' => 250.00,
            'package_price' => 3500.00,
            'price' => 14.00,
        ]);

        foreach ($materials as $item) {
            Material::updateOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
