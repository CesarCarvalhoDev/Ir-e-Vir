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
        Schema::create('stays', function (Blueprint $table) {
            $table->id();
            $table->dateTime('entry');
            $table->dateTime('exit')->nullable();
            $table->integer('total_time')->nullable();
            $table->foreignId('vehicle_id')->references('id')->on('vehicles');
            $table->foreignId('zone_id')->references('id')->on('zones');
            $table->enum('status', [
                'ACTIVE',
                'FINISHED',
                'IRREGULAR'
            ])->default('ACTIVE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stays');
    }
};
