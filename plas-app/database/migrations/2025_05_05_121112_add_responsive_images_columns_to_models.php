<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add responsive image columns to news table
        Schema::table('news', function (Blueprint $table) {
            $table->string('image_path_small')->nullable()->after('image_path');
            $table->string('image_path_medium')->nullable()->after('image_path');
            $table->string('image_path_large')->nullable()->after('image_path');
        });

        // Add responsive image columns to healthcare_providers table
        Schema::table('healthcare_providers', function (Blueprint $table) {
            $table->string('logo_path_small')->nullable()->after('logo_path');
            $table->string('logo_path_medium')->nullable()->after('logo_path');
            $table->string('logo_path_large')->nullable()->after('logo_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove responsive image columns from news table
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn(['image_path_small', 'image_path_medium', 'image_path_large']);
        });

        // Remove responsive image columns from healthcare_providers table
        Schema::table('healthcare_providers', function (Blueprint $table) {
            $table->dropColumn(['logo_path_small', 'logo_path_medium', 'logo_path_large']);
        });
    }
};
