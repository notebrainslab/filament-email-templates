# Filament Email Templates Manager
A powerful and flexible Email Template Management plugin for Filament v4 and v5.

This plugin allows you to manage email templates directly from your Filament panel, bind them to standard Laravel events, and automatically send dynamic emails with full Blade syntax support.

## ✨ Features
- Store email templates in the database.
- Assign templates to standard Laravel events simply by using their class name.
- Automatically send emails when events are fired.
- Access all public properties of the dispatched event inside templates via Blade attributes (e.g. `{{ $user->email }}`).
- Define dynamic or static recipients, CC, and BCC per event.
- Define dynamic attachments utilizing model attributes.
- Full Blade syntax support inside email templates.
- Integrated **Unlayer** editor for visual email design.
- Send emails immediately or delayed (using Laravel Queues).
- Handle template errors safely with a built-in Exception viewer.
- Compatible with **Filament v4** & **v5**.

## 🚀 Installation

Install the package via composer:

```bash
composer require notebrainslab/filament-email-templates
```

Run the install command. This command will publish migrations and configurations, and offer to run migrations:

```bash
php artisan filament-email-templates:install
```

If you chose not to run migrations during install, you can do so manually:

```bash
php artisan migrate
```

## 🛠 Configuration

Publish the config file (optional, if you skipped installation command):

```bash
php artisan vendor:publish --tag="filament-email-templates-config"
```

The configuration allows setting your Unlayer Project ID. By default, Unlayer handles a free visual builder locally without storing it remotely.

```php
return [
    'unlayer_project_id' => env('UNLAYER_PROJECT_ID', null),
];
```

Register the Plugin in your Filament Panel Provider (usually `app/Providers/Filament/AdminPanelProvider.php`):

```php
use NoteBrainsLab\FilamentEmailTemplates\FilamentEmailTemplatesPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugins([
            FilamentEmailTemplatesPlugin::make(),
        ]);
}
```

## 💡 Usage

### Managing Templates
Navigate to the "Settings" section of your Filament Panel and click on "Email Templates".
- Fill out the basic info: **Name**, **Event Class** (e.g., `App\Events\OrderPlaced`).
- Build your subject line dynamically (e.g., `Order #{{ $order->id }} Received!`).
- Add recipients. You can type fixed emails `admin@example.com` or Blade output `{{ $order->user->email }}`.
- Switch to the "Design" tab. The Unlayer editor will load perfectly. Build your HTML, drag components, and save!

### Laravel Events
Create standard Laravel Events. Ensure that they have *public properties*. The available contexts in Blade are derived from these properties.

```php
namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class OrderPlaced
{
    use Dispatchable, SerializesModels;

    public Order $order; // Available as {{ $order }} in email template blade

    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
```

When you dispatch the event, the package automatically captures it. If a matching active template exists, it will dispatch a queue job to render the blade and attach files, and send it through Laravel Mail.

```php
OrderPlaced::dispatch($order);
```

### Delaying Emails
In the template editor, you can set a `delay in minutes` value (e.g., 60). Laravel will wait 60 minutes before broadcasting the customized e-mail!

### Exception Handling
If an email crashes on send (for instance, a typo in the Blade template `{{ $usser->email }}` instead of `{{ $user->email }}`), your application will **NOT** crash. The background Queue will fail to prevent a loop, and the error details alongside the exact payload passed into Blade will be recorded securely in the **Email Template Exceptions** resource, where you can inspect it visually.

## Security Vulnerabilities
If you discover a security vulnerability within this package, please send an e-mail to NoteBrainsLab via `info@notebrainslab.com`.

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
