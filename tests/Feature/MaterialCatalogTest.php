<?php

namespace Tests\Feature;

use App\Livewire\MaterialCatalog;
use Database\Seeders\MaterialSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MaterialCatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(MaterialSeeder::class);
    }

    public function test_material_catalog_component_renders_grouped_materials(): void
    {
        Livewire::test(MaterialCatalog::class)
            ->assertSee('Daftar Harga Transparan')
            ->assertSee('Kategori: Daging')
            ->assertSee('Kategori: Tepung')
            ->assertSee('Kategori: Bumbu')
            ->assertSee('Daging Sapi')
            ->assertSee('Daging Ayam')
            ->assertSee('Tepung Tapioka')
            ->assertSee('Bawang Putih Kupas')
            ->assertSee('Garam Kasar')
            ->assertSee('Jasa Selepan (Penggilingan)');
    }

    public function test_welcome_page_contains_material_catalog(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('id="daftar-harga"', false);
        $response->assertSee('Daftar Harga Transparan');
    }
}
