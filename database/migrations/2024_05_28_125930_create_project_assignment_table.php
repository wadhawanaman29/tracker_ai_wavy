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
        Schema::create('project_assignment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assigned_by'); // Foreign key column
            $table->foreign('assigned_by')->references('id')->on('users');
            $table->unsignedBigInteger('assigned_to'); // Foreign key column
            $table->foreign('assigned_to')->references('id')->on('users');
            $table->string('project');
            $table->text('comment');
            $table->integer('project_time');
            $table->enum('assignment_status', ['1', '0'])->default('0');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_assignment');
    }
};
