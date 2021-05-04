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
            $table->id();
            $table->string('name');
            $table->string('alias')->unique();
            $table->text('brief')->nullable();
            $table->text('description')->nullable();
            $table->string('email')->nullable();
            $table->string('sms_keyword')->nullable();
            $table->string('phone_open')->nullable();
            $table->foreignId('category_id')->nullable();
            $table->timestamp('begin_at')->default('2021-01-01 00:00:00');
            $table->timestamp('end_at')->nullable();
            // avatar images/program_banners/ee_prog_banner.png
            // cover images/program_banners/ee_prog_banner.png
            // $table->string('announcers_text')->nullable();
            // cbox_uri
            // https://www4.cbox.ws/box/?boxid=4327572&boxtag=Cf5HyA&tid=40&tkey=0a03cfb268e04305
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
        Schema::dropIfExists('programs');
    }
}
