<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_type')->nullable();
            $table->string('media_type')->nullable();
            $table->string('filename');
            $table->string('description')->nullable();
            $table->string('path');
            $table->string('mime_type')->nullable();
            $table->string('extension', 6)->nullable();
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
        Schema::dropIfExists('media');
    }
};
