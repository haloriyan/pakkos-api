<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->string('consumer_name')->nullable();
            $table->text('description');
            $table->string('province');
            $table->string('city');
            $table->string('subdistrict');
            $table->text('address');
            $table->string('address_note')->nullable();
            $table->bigInteger('price');
            $table->text('price_inclusion')->nullable();
            $table->string('room_size');
            $table->integer('room_total');
            $table->integer('room_available');
            $table->string('front_building_photo');
            $table->string('inside_building_photo');
            $table->string('streetview_building_photo');
            $table->string('front_room_photo');
            $table->string('inside_room_photo');
            $table->string('bath_room_photo');
            $table->string('other_photo')->nullable();
            $table->boolean('is_approved')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
