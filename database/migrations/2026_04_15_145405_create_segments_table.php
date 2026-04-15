<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transcription_file_id')->constrained('transcription_files')->onDelete('cascade');
            $table->string('speaker');
            $table->string('start_time');
            $table->string('end_time');
            $table->text('source_text');
            $table->text('translated_text')->nullable();
            $table->enum('status', ['new', 'translated', 'reviewed'])->default('new');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('segments');
    }
};
