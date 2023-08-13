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
        Schema::create('users', function (Blueprint $table) {
            $table->id('ProductID');
            $table->string('Type')->nullable();
            $table->string('ClassName')->nullable();
            $table->string('PurchasePrice')->nullable();
            $table->string('SellPrice')->nullable();
            $table->tinyInteger('Bonus')->default(0);
            $table->tinyInteger('CardCash')->default(0);
            $table->tinyInteger('Monitor')->default(0);
            $table->dateTime('parseDate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};
