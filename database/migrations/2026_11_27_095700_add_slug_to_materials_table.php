<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table): void {
            $table->string('slug', 120)->nullable()->unique()->after('name');
        });

        // Backfill slug kanonik untuk seluruh data bahan yang sudah ada agar kalkulator tidak bergantung pada pencocokan string nama
        $canonicalMap = [
            'daging sapi' => 'daging-sapi',
            'daging ayam' => 'daging-ayam',
            'tepung tapioka' => 'tepung-tapioka',
            'tepung probakso' => 'tepung-probakso',
            'tepung terigu' => 'tepung-terigu',
            'tepung sagu' => 'tepung-sagu',
            'bawang putih' => 'bawang-putih-kupas',
            'garam' => 'garam-kasar',
        ];

        $usedSlugs = [];
        $rows = DB::table('materials')->orderBy('id')->get(['id', 'name']);

        foreach ($rows as $row) {
            $lowerName = strtolower(trim((string) $row->name));
            $baseSlug = null;

            foreach ($canonicalMap as $needle => $canonicalSlug) {
                if (str_contains($lowerName, $needle) && ! isset($usedSlugs[$canonicalSlug])) {
                    $baseSlug = $canonicalSlug;
                    break;
                }
            }

            if ($baseSlug === null) {
                $baseSlug = Str::slug((string) $row->name) ?: 'bahan-'.$row->id;
            }

            $slug = $baseSlug;
            $suffix = 2;
            while (isset($usedSlugs[$slug])) {
                $slug = $baseSlug.'-'.$suffix;
                $suffix++;
            }

            $usedSlugs[$slug] = true;

            DB::table('materials')
                ->where('id', $row->id)
                ->update(['slug' => $slug]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table): void {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
