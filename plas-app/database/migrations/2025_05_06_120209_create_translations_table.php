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
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 10)->index(); // Language code: en, fr, ig, etc.
            $table->string('namespace', 100)->default('*')->index(); // For grouping translations
            $table->string('group', 100)->index(); // File/group name (e.g., general, admin, etc.)
            $table->string('key')->index(); // Translation key
            $table->text('value')->nullable(); // Translated text
            $table->boolean('is_custom')->default(false); // Flag for custom translations vs. file-based
            $table->timestamp('last_used_at')->nullable(); // For tracking usage
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            // Create a unique index for the combination of locale, namespace, group, and key
            $table->unique(['locale', 'namespace', 'group', 'key'], 'translations_unique_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
