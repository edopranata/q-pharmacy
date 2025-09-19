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
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_login')->nullable()->after('email_verified_at');
            $table->string('avatar')->nullable()->after('last_login');
            $table->timestamp('last_activity')->nullable()->after('last_login');
            $table->string('phone')->nullable()->after('avatar');
            $table->text('bio')->nullable()->after('phone');
            $table->string('location')->nullable()->after('bio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['users_last_activity_index']);
            $table->dropColumn(['last_login', 'avatar', 'phone', 'bio', 'location', 'last_activity']);
        });
    }
};
