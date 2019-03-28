<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name', 60)->comment('文档名');
            $table->string('filename', 60)->comment('文件存储名');

            $table->integer('type')->comment("文件类型");
            $table->unsignedInteger('size');
            $table->string('tag');

            $table->unsignedInteger('uploader');
            $table->unsignedInteger('downloads')->default(0);

            $table->string('title', 60);
            $table->text('description');
            $table->integer('score');

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
        Schema::dropIfExists('documents');
    }
}
