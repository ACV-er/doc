<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecoursesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('recourses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('presenter')->comment("发起者id");
            $table->string('title', 300);
            $table->text('context');
            $table->integer('score')->comment("悬赏积分");
            $table->integer('helper')->comment("解决者")->default(-1);
            $table->integer("solution")->comment("解决方案")->default(-1);
            $table->json("solutions")->comment("提交的解答,文件id");
            $table->boolean("urgent")->comment("紧急");
            $table->string('tag');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('recourses');
    }
}
