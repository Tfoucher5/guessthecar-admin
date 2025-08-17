<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBouncerTables extends Migration
{
    public function up()
    {
        Schema::create('abilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('entity_type')->nullable();
            $table->timestamps();
            $table->unique(['name', 'entity_type']);
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title')->nullable();
            $table->timestamps();
            $table->unique('name');
        });

        Schema::create('assigned_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->morphs('entity');
            $table->timestamps();
            $table->primary(['role_id', 'entity_id', 'entity_type']);
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('ability_id');
            $table->morphs('entity');
            $table->boolean('forbidden')->default(false);
            $table->timestamps();
            $table->primary(['ability_id', 'entity_id', 'entity_type']);
            $table->foreign('ability_id')->references('id')->on('abilities')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('assigned_roles');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('abilities');
    }
}
