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
        // Schema::create('emails', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('email')->unique();
        //     $table->timestamps(); // Creates `created_at` and `updated_at` columns
        // });

        Schema::create('general_setting', function (Blueprint $table) {
            $table->id();
            $table->text('setting_key')->unique();
            $table->text('setting_value');
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('emails');
        Schema::dropIfExists('general_setting');
    }
};
