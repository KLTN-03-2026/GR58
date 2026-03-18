<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chạy các migration.
     */
    public function up(): void
    {
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('khach_hang_id')->constrained('khach_hangs')->onDelete('cascade');
            $table->string('provider'); // google, facebook
            $table->string('provider_user_id'); // ID từ Google/Facebook
            $table->timestamps();
        });
    }

    /**
     * Hoàn tác các migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_accounts');
    }
};
