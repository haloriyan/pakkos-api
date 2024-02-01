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
        Schema::create('reservation_forms', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('reservation_id')->unsigned()->index();
            $table->foreign('reservation_id')->references('id')->on('listings')->onDelete('cascade');
            $table->bigInteger('template_id')->unsigned()->index()->nullable();
            $table->foreign('template_id')->references('id')->on('templates')->onDelete('set null');
            $table->text('answer');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_forms');
    }
};
