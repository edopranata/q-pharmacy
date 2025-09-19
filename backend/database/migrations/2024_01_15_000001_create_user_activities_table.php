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
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action', 100)->index(); // e.g., 'login', 'view_users', 'create_product'
            $table->string('route', 255)->index(); // API route or page path
            $table->string('method', 10)->default('GET'); // HTTP method
            $table->ipAddress('ip_address')->index(); // User's IP address
            $table->text('user_agent')->nullable(); // Browser/device info
            $table->json('metadata')->nullable(); // Additional data (request params, etc.)
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index(['ip_address', 'created_at']);
            $table->index('created_at'); // For cleanup operations
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};