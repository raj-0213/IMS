<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublishedProductsTable extends Migration
{
    public function up()
    {
        Schema::create('published_products', function (Blueprint $table) {
            $table->id();
            $table->integer('ws_code')->unique();
            $table->string('name');
            $table->float('sales_price');
            $table->float('mrp');
            $table->string('manufacturer_name');
            $table->boolean('is_banned')->default(false);
            $table->boolean('is_discontinued')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_assured')->default(false);;
            $table->boolean('is_refrigerated')->default(false);;
            $table->foreignId('category_id')->constrained('categories');
            $table->text('combination');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('published_products');
    }
}