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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('listing_id')->unsigned()->index();
            $table->foreign('listing_id')->references('id')->on('listings')->onDelete('cascade');
            $table->bigInteger('type_id')->unsigned()->index();
            $table->foreign('type_id')->references('id')->on('listing_types')->onDelete('cascade');
            $table->bigInteger('host_id')->unsigned()->index();
            $table->foreign('host_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('resident_id')->unsigned()->index();
            $table->foreign('resident_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('amount');
            $table->dateTime('payment_issued');
            $table->string('payment_period'); // monthly, quarterly, semi-annually, annually
            $table->string('payment_method')->nullable();
            $table->string('payment_channel')->nullable();
            $table->string('payment_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
