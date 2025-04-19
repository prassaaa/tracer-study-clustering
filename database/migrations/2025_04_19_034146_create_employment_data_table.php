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
        Schema::create('employment_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alumni_id');
            $table->string('company_name');
            $table->string('position');
            $table->string('industry');
            $table->decimal('salary', 12, 2)->nullable();
            $table->integer('waiting_period')->comment('In months'); // Waktu tunggu kerja
            $table->boolean('is_relevant')->default(true); // Relevansi dengan jurusan
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current_job')->default(true);
            $table->timestamps();
            
            $table->foreign('alumni_id')
                  ->references('id')
                  ->on('alumni')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employment_data');
    }
};
