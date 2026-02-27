<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Tests;

use NoteBrainsLab\FilamentEmailTemplates\FilamentEmailTemplatesServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->artisan('view:clear');
    }

    protected function getPackageProviders($app)
    {
        return [
            FilamentEmailTemplatesServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function defineDatabaseMigrations()
    {
        Schema::create('filament_email_themes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->longText('header_html')->nullable();
            $table->longText('footer_html')->nullable();
            $table->longText('custom_css')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('filament_email_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('theme_id')->nullable();
            $table->string('name')->unique();
            $table->string('key')->index();
            $table->string('locale')->default('en')->index();
            $table->string('subject');
            $table->longText('body_html')->nullable();
            $table->json('body_json')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['key', 'locale']);
        });

        Schema::create('filament_email_template_exceptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('email_template_id');
            $table->string('event_class')->nullable();
            $table->json('payload')->nullable();
            $table->text('error_message');
            $table->longText('trace');
            $table->timestamps();
        });
    }
}
