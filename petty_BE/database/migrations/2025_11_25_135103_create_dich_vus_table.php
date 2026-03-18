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
        Schema::create('dich_vus', function (Blueprint $table) {
            $table->id();
            // service name
            $table->string('ten')->index();

            // price (stored as decimal)
            $table->decimal('gia_tien', 15, 2)->default(0);

            // duration in minutes
            $table->unsignedInteger('thoi_gian_thuc_hien')->nullable()->comment('minutes');

            // description
            $table->text('mo_ta')->nullable();

            // status: 'kinh_doanh' (in business) or 'ngung' (stopped)
            // use enum to restrict values
            $table->enum('trang_thai', ['kinh_doanh', 'ngung'])->default('kinh_doanh')->index();

            // category relationship (danh_muc_dich_vus) - keep as unsignedBigInteger nullable
            // to avoid FK creation ordering issues; add FK in a later migration if needed
            $table->unsignedBigInteger('danh_muc_id')->nullable()->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dich_vus');
    }
};
