<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaigns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_shop');
            $table->foreign('id_shop')->references('id')->on('shops');
            $table->string('name',150);
            $table->string('thumb',255);
            $table->string('subject',78);
            $table->text('email_content');
            $table->text('email_footer');
            $table->text('customize_email');
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
        
        Schema::dropIfExists('campaigns');
    }
}
