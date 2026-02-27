<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Tests;

use NoteBrainsLab\FilamentEmailTemplates\FilamentEmailTemplatesServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // This clears the view cache, otherwise pest tests might fail due to blade cache conflicts
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
        // Use an in-memory SQLite database
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Define migrations
        $migration = include __DIR__.'/../database/migrations/create_filament_email_templates_table.php.stub';
        $migration->up();

        $migrationExceptions = include __DIR__.'/../database/migrations/create_filament_email_template_exceptions_table.php.stub';
        $migrationExceptions->up();
    }
}
