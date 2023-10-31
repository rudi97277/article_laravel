<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembershipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique()->nullable();
            $table->string('name');
            $table->string('status');
            $table->string('link_schooler');
            $table->string('link_scoopus');
            $table->string('email')->unique();
            $table->uuid('evidence_id')->unique()->nullable();
            $table->boolean('verified')->default(0);
            $table->foreign('evidence_id')->references('id')->on('documents');
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
        Schema::dropIfExists('memberships');
    }
}
