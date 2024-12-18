<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('current_users', 20);
            $table->string('action_type', 50);
            $table->string('table_name');
            $table->integer('record_id');
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->timestamps(0); // To match TIMESTAMPTZ
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
}
