<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            // Change the searchable_content column from TEXT to MEDIUMTEXT
            DB::statement('ALTER TABLE resources MODIFY searchable_content MEDIUMTEXT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            // Change it back to TEXT if needed
            DB::statement('ALTER TABLE resources MODIFY searchable_content TEXT');
        });
    }
};
