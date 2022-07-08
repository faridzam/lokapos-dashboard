<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('user_roles')) {

            Schema::create('user_roles', function (Blueprint $table) {
                
                $table->id();
                $table->enum('department', ['Information & Technology', 'Inpark Revenue', 'Finance', 'others']);
                $table->enum('levels', ['ENGINEER', 'director', 'manager', 'spv', 'admin', 'staff', 'viewer']);
                $table->boolean('dashboard');
                $table->boolean('accounts');
                $table->boolean('report-sales');
                $table->boolean('report-itemSales');
                $table->boolean('data-item');
                $table->boolean('void');
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
        Schema::dropIfExists('user_roles');
    }
}
