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
        Schema::create('tie_points', function (Blueprint $table) {
            $table->id();
            $table->string('location');
            $table->string('city_municipality');
            $table->string('point_of_reference');
            $table->float('latitude',6);
            $table->float('longitude',6);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tie_points');
    }
};
