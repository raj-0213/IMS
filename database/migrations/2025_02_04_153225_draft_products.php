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
            $table->string('name', 255);
            $table->decimal('sales_price', 10, 2);
            $table->decimal('mrp', 10, 2);
            $table->string('manufacturer_name', 255);
            $table->boolean('is_banned')->default(false);
            $table->boolean('is_discontinued')->default(false);
            $table->boolean('is_assured')->default(false);
            $table->boolean('is_refrigerated')->default(false);
            $table->foreignId('category_id')->constrained('categories');
            $table->enum('product_status', ['Draft', 'Published', 'Unpublished'])->default('Draft');
            $table->string('ws_code')->nullable()->default('null');
            $table->text('combination');
            $table->foreignId('published_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->timestamps();
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('restrict');
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
