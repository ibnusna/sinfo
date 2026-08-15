<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'scf';

    public function up(): void
    {
        Schema::connection('scf')->table('assessments', function (Blueprint $table) {
            // assessment_type: lebih spesifik dari assessor_type
            // poster = guru menilai poster, science = guru menilai IPA, food = juri menilai makanan
            $table->enum('assessment_type', ['poster', 'science', 'food'])
                ->nullable()
                ->after('assessor_type');

            $table->index(['program_id', 'group_id', 'assessment_type']);
        });

        // Isi assessment_type berdasarkan assessor_type yang ada
        // guru → science (default), juri → food
        // Data lama dengan kriteria yang salah tetap dibiarkan (tidak dihapus strukturnya,
        // tapi tidak akan masuk kalkulasi karena kriteria baru ada di assessment_criteria)
        \DB::connection('scf')->table('assessments')->where('assessor_type', 'guru')->update(['assessment_type' => 'science']);
        \DB::connection('scf')->table('assessments')->where('assessor_type', 'juri')->update(['assessment_type' => 'food']);
    }

    public function down(): void
    {
        Schema::connection('scf')->table('assessments', function (Blueprint $table) {
            $table->dropIndex(['program_id', 'group_id', 'assessment_type']);
            $table->dropColumn('assessment_type');
        });
    }
};
