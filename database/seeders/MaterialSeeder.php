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
                'unit' => 'gram',
                'price' => 140.00, // Rp140.000 / kg -> Rp140 / gram
                'is_available' => true,
            ],
            [
                'name' => 'Daging Ayam',
                'category' => 'daging',
                'unit' => 'gram',
                'price' => 40.00, // Rp40.000 / kg -> Rp40 / gram
                'is_available' => true,
            ],
            [
                'name' => 'Tepung Tapioka',
                'category' => 'tepung',
                'unit' => 'gram',
                'price' => 18.00, // Rp9.000 / 500 gram -> Rp18 / gram
                'is_available' => true,
            ],
            [
                'name' => 'Tepung ProBakso',
                'category' => 'tepung',
                'unit' => 'gram',
                'price' => 40.00, // Rp10.000 / 250 gram -> Rp40 / gram
                'is_available' => true,
            ],
            [
                'name' => 'Tepung Terigu',
                'category' => 'tepung',
                'unit' => 'gram',
                'price' => 10.00, // Rp10.000 / kg -> Rp10 / gram
                'is_available' => true,
            ],
            [
                'name' => 'Tepung Sagu',
                'category' => 'tepung',
                'unit' => 'gram',
                'price' => 20.00, // Rp5.000 / 250 gram -> Rp20 / gram (Merek Dorang/Irasa)
                'is_available' => true,
            ],
            [
                'name' => 'Bawang Putih Kupas',
                'category' => 'bumbu',
                'unit' => 'gram',
                'price' => 40.00, // Rp4.000 / 100 gram -> Rp40 / gram
                'is_available' => true,
            ],
            [
                'name' => 'Bawang Merah Goreng',
                'category' => 'bumbu',
                'unit' => 'gram',
                'price' => 80.00, // Rp8.000 / 100 gram -> Rp80 / gram
                'is_available' => true,
            ],
            [
                'name' => 'Garam Kasar',
                'category' => 'bumbu',
                'unit' => 'gram',
                'price' => 3.00, // Rp3.000 / kg -> Rp3 / gram
                'is_available' => true,
            ],
            [
                'name' => 'Telur Ayam',
                'category' => 'pengikat',
                'unit' => 'butir',
                'price' => 2000.00, // Rp2.000 / butir
                'is_available' => true,
            ],
            [
                'name' => 'Merica Bubuk Sachet',
                'category' => 'bumbu',
                'unit' => 'pack',
                'price' => 1000.00, // Rp1.000 / sachet
                'is_available' => true,
            ],
            [
                'name' => 'Pengenyal Sobaso',
                'category' => 'tambahan',
                'unit' => 'pack',
                'price' => 10000.00, // Rp10.000 / pack (250g)
                'is_available' => true,
            ],
            [
                'name' => 'Penyedap Masako',
                'category' => 'penyedap',
                'unit' => 'pack',
                'price' => 500.00, // Rp500 / sachet
                'is_available' => true,
            ],
        ];

        foreach ($materials as $item) {
            Material::updateOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
