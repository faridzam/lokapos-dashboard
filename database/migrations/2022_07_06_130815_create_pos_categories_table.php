<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('pos_categories')) {

            Schema::create('pos_categories', function (Blueprint $table) {
                $table->id();
                $table->string('category_id');
                $table->string('name');
                $table->enum('type', ['makanan', 'minuman', 'non-konsumsi'])->nullable();
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
        Schema::dropIfExists('pos_categories');
    }
}
