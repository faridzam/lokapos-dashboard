<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('pos_stores')) {

            Schema::create('pos_stores', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('store_id');
                $table->string('name');
                $table->string('ip_address_mobile')->nullable();
                $table->enum('type', ['fnb', 'retail', 'others', 'rental']);
                $table->enum('area', ['downtown', 'pesisir', 'balalantara', 'kamayayi', 'ararya', 'segara prada', 'others']);
                $table->enum('platform', ['desktop', 'mobile']);
                $table->boolean('isActive')->default(true);
                $table->timestamps();
            });

        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pos_stores');
    }
}
