<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('documents', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name', 60)->comment('文档名');
            $table->string('filename', 60)->comment('文件存储名');

            $table->integer('type')->comment("文件类型");
            $table->unsignedInteger('size');
            $table->string('tag');

            $table->unsignedInteger('uploader');
            $table->foreign('uploader')->references('id')->on('users');
            $table->string('uploader_nickname');
            $table->unsignedInteger('downloads')->default(0);

            $table->string('title', 60);
            $table->text('description');
            $table->integer('score')->default(0);
            $table->string('md5', 65);
<<<<<<< HEAD
            $table->integer('page')->default(-1)->comment("文档页数，-1为不可预览");
=======
>>>>>>> ac160807170520091ad647049f8e571ed0233018

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('documents');
    }
}
