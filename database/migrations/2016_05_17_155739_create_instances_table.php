<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instances', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('websocket_proxy_url');
            $table->string('outbound_proxy');
            $table->string('ice_servers');
            $table->string('rtcweb_breaker');
            $table->string('bandwidth');
            $table->string('video_enable');
            $table->string('video_size');
            $table->timestamps();
        });

        Schema::create('instance_extension', function (Blueprint $table) {
            $table->integer('instance_id')->unsigned();
            $table->integer('extension_id')->unsigned();

            $table->foreign('instance_id')
                ->references('id')
                ->on('instances')
                ->onDelete('cascade');

            $table->foreign('extension_id')
                ->references('id')
                ->on('extensions')
                ->onDelete('cascade');

            $table->primary(['instance_id', 'extension_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('instance_extension');
        Schema::drop('instances');
    }
}
