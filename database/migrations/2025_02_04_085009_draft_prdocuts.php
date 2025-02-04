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
        Schema::create('draft_products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name', 255);
            $table->string('product_code', 100)->unique();
            $table->string('manufacture_name', 255)->nullable();
            $table->decimal('mrp', 10, 2)->nullable();
            $table->boolean('is_banned')->default(false);
            $table->boolean('is_discontinued')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_assured')->default(false);
            $table->boolean('is_refrigerated')->default(false);
            $table->boolean('is_published')->default(false);
            $table->string('molecules', 255)->nullable();
            $table->foreignId('category_id')->constrained('categories');
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('is_activated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('draft_products');
    }
};
