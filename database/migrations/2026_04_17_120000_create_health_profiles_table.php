<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Encrypted specific values
            $table->text('weight')->nullable(); 
            $table->text('height')->nullable(); 
            
            // Plain text aggregatable data (anonymized for trends)
            $table->string('bmi_category')->nullable()->index(); // e.g. Normal, Overweight
            $table->string('activity_level')->nullable()->index(); // e.g. Rendah, Sedang, Tinggi
            
            // Encrypted Health Statuses
            $table->text('blood_pressure_status')->nullable();
            $table->text('blood_sugar_status')->nullable();
            $table->text('cholesterol_status')->nullable();
            
            // Encrypted Early Warning Symptoms & Recommendation
            $table->text('last_symptoms')->nullable();
            $table->text('ai_recommendation')->nullable();
            
            $table->date('last_checkup_date')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_profiles');
    }
};
