<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnouncerHasProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcer_has_programs', function (Blueprint $table) {
            
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('announcer_id');


            $table->foreign('program_id')
                ->references('id')
                ->on('programs')
                ->onDelete('cascade');

            $table->foreign('announcer_id')
                ->references('id')
                ->on('announcers')
                ->onDelete('cascade');

            $table->primary(['program_id', 'announcer_id'], 'announcer_has_programs_program_id_announcer_id_primary');
        });

    }


    



    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('announcer_has_programs');
    }
}
