<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZonesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('zones', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('template_id')->unsigned();
			$table->foreign('template_id')->references('id')->on('templates');
			$table->datetime('effect_date')->nullable();
			$table->string('name', 20);
			$table->enum('qualifyrule', ['Acceptable','Decline', 'Needs Approval'])->default('Acceptable');
			$table->text('messagetouser')->nullable();
			$table->enum('zonetype', ['Select One','Within Zone', 'Distance to Zone'])->default('Select One');
			$table->text('adminnote')->nullable();
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
		Schema::drop('zones');
	}

}
