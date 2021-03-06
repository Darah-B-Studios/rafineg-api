<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use PhpParser\Node\Stmt\Enum_;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('code')->nullable();
            $table->string('reference')->unique();
            $table->string('phoneNumber');
            $table->integer('amount');
            $table->string('description')->nullable();
            $table->string('currency')->nullable();
            $table->string('operator')->nullable();
            $table->string('operatorReference')->nullable();
            $table->string('externalReference')->nullable();
            $table->string('status')->default('PENDING');
            $table->string('collectionType')->nullable();
            $table->string('collectionTypeCode')->nullable();
            $table->integer('oldBalance')->default(0);
            $table->integer('newBalance')->default(0);
            $table->string('signature')->nullable();
            $table->string('method')->default(Config::get('app.transaction_method.momo'));
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
        Schema::dropIfExists('transactions');
    }
}