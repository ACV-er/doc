<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nickname');

            $table->string('stu_id');
            $table->string('password');

            $table->string('email')->unique()->nullable();
            $table->string('avatar', 30)->default("default.jpg");
            $table->unsignedInteger('score')->default(0);

            $table->json('download')->default('[]');
            $table->json('upload')->default('[]');
            $table->json('collection')->default('[]');
            $table->json('recourse')->default('[]');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
