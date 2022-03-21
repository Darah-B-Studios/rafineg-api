<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampayTransactionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('campay_transactions', function (Blueprint $table) {
			$table->id();
			$table->string('status');
			$table->string('reference');
			$table->string('amount');
			$table->string('currency');
			$table->string('operator');
			$table->string('code')->unique();
			$table->string('operator_reference');
			$table->string('signature');
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
		Schema::dropIfExists('campay_transactions');
	}
}
