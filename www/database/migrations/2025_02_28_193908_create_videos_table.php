<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('title', 1000);
            $table->enum('status', ['uploading', 'uploaded', 'error']);
            $table->datetimes();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
