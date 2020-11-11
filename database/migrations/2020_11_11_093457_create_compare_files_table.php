<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompareFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compare_files', function (Blueprint $table) {
            $table->id();
            $table->text('web_url');
            $table->string('image');
            $table->string('url_image');
            $table->text('hash_tag');
            $table->integer('is_image_percent')->nullable();
            $table->enum('status',['1','2'])->comment("1 = Match, 2 = Not Match");
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
        Schema::dropIfExists('compare_files');
    }
}
