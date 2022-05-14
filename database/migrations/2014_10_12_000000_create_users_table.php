<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->string('code', 10)->unique(); // username
            $table->string('email', 50);
            $table->string('phone_number', 15);
            $table->string('password');
            $table->string('referedBy', 8)->nullable();
            $table->string('firstname', 20)->nullable();
            $table->string('lastname', 20)->nullable();
            $table->boolean('is_registered')->default(false);
            $table->boolean('isAdmin')->default(false);
            $table->boolean('isVerified')->default(false)->comment('false: user not verified, true: user verified');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
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