<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('filament_email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key')->unique()->index();
            $table->string('subject');

            // Unlayer visual editor exports
            $table->longText('body_html')->nullable(); // Rendered HTML email
            $table->json('body_json')->nullable();     // Unlayer design JSON (for re-editing)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('filament_email_templates');
    }
};
