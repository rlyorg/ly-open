<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnouncersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            // $table->timestamp('birthday')->nullable();
            $table->date('birthday')->nullable();
            $table->text('favlor')->nullable();
            $table->text('testimony')->nullable();
            $table->text('ministry')->nullable();
            $table->text('descripton')->nullable();
            // $table->text('avatar')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('announcers');
    }
}
