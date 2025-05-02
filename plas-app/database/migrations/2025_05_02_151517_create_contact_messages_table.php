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
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            
            // Sender information
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            
            // Message details
            $table->string('subject');
            $table->text('message');
            $table->foreignId('message_category_id')->nullable()->constrained()->nullOnDelete();
            
            // Status tracking
            $table->enum('status', ['new', 'read', 'responded', 'archived'])->default('new');
            $table->boolean('is_read')->default(false);
            
            // Admin tracking
            $table->foreignId('responded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('responded_at')->nullable();
            
            // Archiving
            $table->timestamp('archived_at')->nullable();
            $table->boolean('auto_archive')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
