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
        Schema::create('table_agenda_governments', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->integer('agenda_number');
            $table->dateTime('date');
            $table->string('purpose');
            $table->string('subject');
            $table->string('officer');
            $table->string('department');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_agenda_governments');
    }
};
