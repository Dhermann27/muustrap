<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('display')->nullable();
            $table->integer('order');
            $table->text('blurb')->nullable();
            $table->text('letter')->nullable();
            $table->text('covenant')->nullable();
            $table->text('calendar')->nullable();
            $table->tinyInteger('is_program_housing');
            $table->tinyInteger('is_minor');
            $table->timestamps();
        });
        DB::update('ALTER TABLE programs AUTO_INCREMENT = 1000');

        DB::unprepared('CREATE FUNCTION getprogramidbyname (programname VARCHAR(1024), year INT) RETURNS INT DETERMINISTIC 	BEGIN
 			RETURN(SELECT p.id FROM programs p WHERE p.name LIKE CONCAT(\'%\', programname, \'%\') ORDER BY age_min DESC LIMIT 1);
 		END');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP FUNCTION IF EXISTS getprogramidbyname');
        Schema::dropIfExists('programs');
    }
}
