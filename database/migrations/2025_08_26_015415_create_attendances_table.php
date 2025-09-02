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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('office_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['in', 'out']); // 'in' = check-in, 'out' = check-out
            $table->timestamp('time');           // waktu absensi
            $table->string('address');           // alamat absensi
            $table->decimal('latitude', 10, 8);  // lokasi absensi
            $table->decimal('longitude', 11, 8);
            $table->string('photo');             // path foto selfie
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
