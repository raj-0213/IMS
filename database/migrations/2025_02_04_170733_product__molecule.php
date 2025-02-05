<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('product_molecules', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('product_id');
            // $table->unsignedBigInteger('molecule_id');
            $table->timestamps();
            $table->foreignId('product_id')->constrained('draft_products');
            $table->foreignId('molecule_id')->constrained('molecules');
            // $table->foreign('product_id')->references('id')->on('draft_products');
            // $table->foreign('molecule_id')->references('id')->on('molecules');
        });
    }

    public function down() {
        Schema::dropIfExists('product_molecules');
    }
};