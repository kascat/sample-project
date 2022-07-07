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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('permission_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->enum('role', ['admin', 'member'])
                ->default('admin');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('status', ['pending_password', 'active', 'blocked', 'blocked_by_time'])
                ->default('pending_password');
            $table->dateTime('expires_in')->nullable();
            $table->integer('login_time')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('permission_id', 'fk_u_permission_id')
                ->references('id')
                ->on('permissions')
                ->onUpdate('RESTRICT')
                ->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('fk_u_permission_id');
        });

        Schema::dropIfExists('users');
    }
};
