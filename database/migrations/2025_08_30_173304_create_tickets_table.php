<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('subject');
            $table->text('body');
            $table->enum('status', ['open','in_progress','resolved','closed'])->default('open');
            $table->string('category')->nullable();
            $table->boolean('category_overridden')->default(false);
            $table->text('note')->nullable();
            $table->text('explanation')->nullable();
            $table->decimal('confidence', 3, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};